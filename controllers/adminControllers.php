<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/UASWEBCOK/config/database.php');
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}


// Function to get total number of warga
function getAllWarga()
{
    global $koneksi;
    $sql = "SELECT COUNT(*) AS total FROM warga";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        echo "Error: " . mysqli_error($koneksi);
        return 0;
    }
}

// Function to get count of users by role
function getUserCountByRole($role)
{
    global $koneksi;
    $role = mysqli_real_escape_string($koneksi, $role);

    $sql = "SELECT COUNT(*) AS total FROM users WHERE role = '$role'";
    $result = mysqli_query($koneksi, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        echo "Error: " . mysqli_error($koneksi);
        return 0;
    }
}

// Function to get financial summary
function getFinancialSummary()
{
    global $koneksi;

    // Get total income
    $sqlIncome = "SELECT SUM(amount) AS total FROM financial_transactions WHERE transaction_type = 'income'";
    $incomeResult = mysqli_query($koneksi, $sqlIncome);
    $income = 0;
    if ($incomeResult) {
        $row = mysqli_fetch_assoc($incomeResult);
        $income = $row['total'] ?: 0;
    }

    // Get total expenses
    $sqlExpense = "SELECT SUM(amount) AS total FROM financial_transactions WHERE transaction_type = 'expense'";
    $expenseResult = mysqli_query($koneksi, $sqlExpense);
    $expenses = 0;
    if ($expenseResult) {
        $row = mysqli_fetch_assoc($expenseResult);
        $expenses = $row['total'] ?: 0;
    }

    // Calculate balance
    $balance = $income - $expenses;

    return [
        'pemasukan' => $income,
        'pengeluaran' => $expenses,
        'saldo' => $balance
    ];
}

// Function to get recent transactions
function getRecentTransactions($limit = 5)
{
    global $koneksi;

    $sql = "SELECT 
                transaction_id,
                transaction_date AS date, 
                description, 
                transaction_type AS type, 
                amount,
                category,
                created_by
            FROM financial_transactions 
            ORDER BY transaction_date DESC 
            LIMIT $limit";

    $result = mysqli_query($koneksi, $sql);
    $transactions = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Truncate description if it's too long
            if (strlen($row['description']) > 100) {
                $row['description'] = substr($row['description'], 0, 100) . '...';
            }
            $transactions[] = $row;
        }
    }

    return $transactions;
}

// Function to get meat distribution data
function getMeatDistribution()
{
    global $koneksi;

    // Get total meat distributed
    $sqlTotal = "SELECT SUM(total_daging) AS total FROM meat_distribution";
    $totalResult = mysqli_query($koneksi, $sqlTotal);
    $total = 0;
    if ($totalResult && $row = mysqli_fetch_assoc($totalResult)) {
        $total = $row['total'] ?: 0;
    }

    // Get beef distribution
    $sqlBeef = "SELECT SUM(daging_sapi) AS total FROM meat_distribution";
    $beefResult = mysqli_query($koneksi, $sqlBeef);
    $beef = 0;
    if ($beefResult && $row = mysqli_fetch_assoc($beefResult)) {
        $beef = $row['total'] ?: 0;
    }

    // Get goat distribution
    $sqlGoat = "SELECT SUM(daging_kambing) AS total FROM meat_distribution";
    $goatResult = mysqli_query($koneksi, $sqlGoat);
    $goat = 0;
    if ($goatResult && $row = mysqli_fetch_assoc($goatResult)) {
        $goat = $row['total'] ?: 0;
    }

    // Get pickup status
    $sqlPickedUp = "SELECT COUNT(*) AS total FROM meat_distribution WHERE status = 'sudah_diambil'";
    $pickedUpResult = mysqli_query($koneksi, $sqlPickedUp);
    $pickedUp = 0;
    if ($pickedUpResult && $row = mysqli_fetch_assoc($pickedUpResult)) {
        $pickedUp = $row['total'] ?: 0;
    }

    $sqlPending = "SELECT COUNT(*) AS total FROM meat_distribution WHERE status = 'belum_diambil'";
    $pendingResult = mysqli_query($koneksi, $sqlPending);
    $pending = 0;
    if ($pendingResult && $row = mysqli_fetch_assoc($pendingResult)) {
        $pending = $row['total'] ?: 0;
    }

    return [
        'total_distributed' => $total,
        'beef' => $beef,
        'goat' => $goat,
        'status' => [
            'picked_up' => $pickedUp,
            'pending' => $pending
        ]
    ];
}

// Function to get detailed meat distribution data
function getDetailedMeatDistribution($limit = 10)
{
    global $koneksi;

    $sql = "SELECT 
                md.distribution_id, 
                md.daging_sapi, 
                md.daging_kambing, 
                md.total_daging,
                md.kode_qr,
                md.status,
                md.pickup_date,
                w.nama_lengkap as recipient_name  
            FROM 
                meat_distribution md 
                LEFT JOIN warga w ON md.warga_id = w.warga_id 
            ORDER BY 
                CASE 
                    WHEN md.pickup_date IS NULL THEN 1 
                    ELSE 0 
                END,
                md.pickup_date DESC 
            LIMIT $limit";

    $result = mysqli_query($koneksi, $sql);
    $distributions = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $distributions[] = $row;
        }
    }

    return $distributions;
}

// Function to get monthly transaction statistics for charts
function getMonthlyTransactionStats($year = null)
{
    global $koneksi;

    // If no year provided, use current year
    if (!$year) {
        $year = date('Y');
    }

    $year = mysqli_real_escape_string($koneksi, $year);

    // Get monthly income
    $sqlIncome = "SELECT 
                    MONTH(transaction_date) as month,
                    SUM(amount) as total
                  FROM financial_transactions 
                  WHERE YEAR(transaction_date) = '$year' AND transaction_type = 'income'
                  GROUP BY MONTH(transaction_date)
                  ORDER BY MONTH(transaction_date)";

    $incomeResult = mysqli_query($koneksi, $sqlIncome);
    $monthlyIncome = array_fill(1, 12, 0); // Initialize all months with zero

    if ($incomeResult) {
        while ($row = mysqli_fetch_assoc($incomeResult)) {
            $month = (int)$row['month'];
            $monthlyIncome[$month] = (float)$row['total'];
        }
    }

    // Get monthly expenses
    $sqlExpense = "SELECT 
                    MONTH(transaction_date) as month,
                    SUM(amount) as total
                  FROM financial_transactions 
                  WHERE YEAR(transaction_date) = '$year' AND transaction_type = 'expense'
                  GROUP BY MONTH(transaction_date)
                  ORDER BY MONTH(transaction_date)";

    $expenseResult = mysqli_query($koneksi, $sqlExpense);
    $monthlyExpense = array_fill(1, 12, 0); // Initialize all months with zero

    if ($expenseResult) {
        while ($row = mysqli_fetch_assoc($expenseResult)) {
            $month = (int)$row['month'];
            $monthlyExpense[$month] = (float)$row['total'];
        }
    }

    // Indonesian month names
    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $result = [
        'labels' => array_values($monthNames),
        'income' => array_values($monthlyIncome),
        'expense' => array_values($monthlyExpense)
    ];

    return $result;
}

// Function to get transaction breakdown by category
function getTransactionsByCategory()
{
    global $koneksi;

    // Get income by category
    $sqlIncome = "SELECT 
                    category, 
                    SUM(amount) as total
                FROM financial_transactions 
                WHERE transaction_type = 'income' AND category IS NOT NULL AND category != ''
                GROUP BY category
                ORDER BY total DESC";

    $incomeResult = mysqli_query($koneksi, $sqlIncome);
    $incomeCategories = [];

    if ($incomeResult) {
        while ($row = mysqli_fetch_assoc($incomeResult)) {
            $incomeCategories[$row['category']] = $row['total'];
        }
    }

    // Get expense by category
    $sqlExpense = "SELECT 
                    category, 
                    SUM(amount) as total
                FROM financial_transactions 
                WHERE transaction_type = 'expense' AND category IS NOT NULL AND category != ''
                GROUP BY category
                ORDER BY total DESC";

    $expenseResult = mysqli_query($koneksi, $sqlExpense);
    $expenseCategories = [];

    if ($expenseResult) {
        while ($row = mysqli_fetch_assoc($expenseResult)) {
            $expenseCategories[$row['category']] = $row['total'];
        }
    }

    // Generate random colors for chart
    $generateColors = function ($count) {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $hue = $i * (360 / $count);
            $colors[] = "hsla($hue, 70%, 60%, 0.8)";
        }
        return $colors;
    };

    return [
        'income' => [
            'labels' => array_keys($incomeCategories),
            'data' => array_values($incomeCategories),
            'colors' => $generateColors(count($incomeCategories))
        ],
        'expense' => [
            'labels' => array_keys($expenseCategories),
            'data' => array_values($expenseCategories),
            'colors' => $generateColors(count($expenseCategories))
        ]
    ];
}

// Function to get allocation data for meat distribution
function getMeatAllocationData()
{
    global $koneksi;

    $sql = "SELECT 
                AVG(daging_sapi) AS avg_beef_per_person, 
                AVG(daging_kambing) AS avg_goat_per_person,
                MIN(daging_sapi) AS min_beef,
                MAX(daging_sapi) AS max_beef,
                MIN(daging_kambing) AS min_goat,
                MAX(daging_kambing) AS max_goat
            FROM meat_distribution";

    $result = mysqli_query($koneksi, $sql);
    $allocation = [
        'avg_beef_per_person' => 0,
        'avg_goat_per_person' => 0,
        'min_beef' => 0,
        'max_beef' => 0,
        'min_goat' => 0,
        'max_goat' => 0
    ];

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $allocation = [
            'avg_beef_per_person' => round($row['avg_beef_per_person'], 2),
            'avg_goat_per_person' => round($row['avg_goat_per_person'], 2),
            'min_beef' => round($row['min_beef'], 2),
            'max_beef' => round($row['max_beef'], 2),
            'min_goat' => round($row['min_goat'], 2),
            'max_goat' => round($row['max_goat'], 2)
        ];
    }

    return $allocation;
}

// Function to get pickup verification statistics
function getPickupVerificationStats()
{
    global $koneksi;

    $sql = "SELECT 
                COUNT(CASE WHEN status = 'sudah_diambil' THEN 1 END) AS verified_today,
                COUNT(CASE WHEN status = 'belum_diambil' THEN 1 END) AS pending_verification,
                COUNT(CASE WHEN DATE(pickup_date) = CURDATE() THEN 1 END) AS picked_up_today
            FROM meat_distribution";

    $result = mysqli_query($koneksi, $sql);
    $stats = [
        'verified_today' => 0,
        'pending_verification' => 0,
        'picked_up_today' => 0
    ];

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats = [
            'verified_today' => $row['verified_today'] ?: 0,
            'pending_verification' => $row['pending_verification'] ?: 0,
            'picked_up_today' => $row['picked_up_today'] ?: 0
        ];
    }

    return $stats;
}

// Function to get dashboard data
function getDashboardData()
{
    // Calculate general user statistics
    $wargaTotal = getAllWarga();
    $berqurbanCount = getUserCountByRole('berqurban');
    $panitiaCount = getUserCountByRole('panitia');
    $regularWargaCount = $wargaTotal - $berqurbanCount - $panitiaCount;

    $data = [
        'user_stats' => [
            'warga' => $wargaTotal,
            'berqurban' => $berqurbanCount,
            'panitia' => $panitiaCount,
            'regular' => $regularWargaCount
        ],
        'financial_summary' => getFinancialSummary(),
        'recent_transactions' => getRecentTransactions(),
        'meat_distribution' => getMeatDistribution(),
        'detailed_meat_distribution' => getDetailedMeatDistribution(),
        'monthly_stats' => getMonthlyTransactionStats(),
        'category_breakdown' => getTransactionsByCategory(),
        'meat_allocation' => getMeatAllocationData(),
        'pickup_verification' => getPickupVerificationStats()
    ];

    return $data;
}
