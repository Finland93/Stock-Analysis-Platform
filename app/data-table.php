<?php
require_once '../app/db-config.php';
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

	function generate_table_data($conn, $sector = null) {
    $data = array();
    global $total_pages;

    // Allowlist the table name (only NYSE/NASDAQ are valid)
    $table = (isset($_GET['table']) && $_GET['table'] === 'NASDAQ') ? 'NASDAQ' : 'NYSE';

    $limit = 30;
    $page = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $page = max(1, (int) $_GET['page']);
    }
    $offset = ($page - 1) * $limit;

    // Optional sector filter is bound as a parameter (prevents SQL injection)
    $where = $sector ? ' WHERE sector = ? AND market_cap != 0' : '';

    // Total row count
    $total_rows = 0;
    if ($stmt_count = mysqli_prepare($conn, "SELECT COUNT(*) AS total_rows FROM $table" . $where)) {
        if ($sector) {
            mysqli_stmt_bind_param($stmt_count, 's', $sector);
        }
        mysqli_stmt_execute($stmt_count);
        $res_count = mysqli_stmt_get_result($stmt_count);
        if ($res_count && ($rc = mysqli_fetch_assoc($res_count))) {
            $total_rows = (int) $rc['total_rows'];
        }
        mysqli_stmt_close($stmt_count);
    }
    $total_pages = $limit > 0 ? ceil($total_rows / $limit) : 0;

    // Main query. Column names are hardcoded and sort order is whitelisted to
    // ASC/DESC, so ORDER BY is safe; sector/limit/offset are bound.
    $sql = "SELECT symbol, name, sector, peRatio, market_cap, pbRatio, rsiAnalysis, eps FROM $table" . $where;

    $sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc') ? 'DESC' : 'ASC';
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : '';
    $order_columns = array(
        'CAP'       => "$table.market_cap",
        'RSI'       => "$table.rsiAnalysis",
        'PE'        => "$table.peRatio",
        'PB'        => "$table.pbRatio",
        'EPS'       => "$table.eps",
        'RSI_PE_PB' => "(rsiAnalysis + peRatio + pbRatio)",
    );
    if (isset($order_columns[$sort_by])) {
        $sql .= ' ORDER BY ' . $order_columns[$sort_by] . ' ' . $sort_order;
    }

    $sql .= ' LIMIT ? OFFSET ?';

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return $data;
    }
    if ($sector) {
        mysqli_stmt_bind_param($stmt, 'sii', $sector, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Market capitalization clean text
            $market_cap = $row['market_cap'];
            if ($market_cap >= 1000000000000) {
                $market_cap = round($market_cap / 1000000000000, 2) . ' T';
            } else if ($market_cap >= 1000000000) {
                $market_cap = round($market_cap / 1000000000, 2) . ' B';
            } else if ($market_cap >= 1000000) {
                $market_cap = round($market_cap / 1000000, 2) . ' M';
            } else if ($market_cap >= 1000) {
                $market_cap = round($market_cap / 1000, 2) . ' K';
            }

            $data[] = array(
                'symbol' => $row['symbol'],
                'name' => $row['name'],
                'market_cap' => $row['market_cap'],
                'eps' => $row['eps'],
                'market_cap_string' => $market_cap,
                'pe_ratio' => round($row['peRatio'], 2),
                'pb_ratio' => round($row['pbRatio'], 2),
                'rsi_analysis' => round($row['rsiAnalysis'], 2)
            );
        }
    }
    mysqli_stmt_close($stmt);
    return $data;
}

function generate_table_html($data) {
	// Sanitize/allowlist user-controlled query params before echoing them (XSS)
	$allowed_sort = array('CAP', 'PE', 'PB', 'RSI', 'EPS', 'RSI_PE_PB');
	$sort_by = (isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowed_sort, true)) ? $_GET['sort_by'] : '';
	$sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] === 'asc') ? 'asc' : ((isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc') ? 'desc' : '');
	$html = '<div class="stock-listings">';
	$html .= '<div class="stock-listings-buttons">';
	$html .= '<button class="nyse-button" data-type="nyse"><a href="?table=NYSE">NYSE</a></button>';
	$html .= '<button class="nasdaq-button" data-type="nasdaq"><a href="?table=NASDAQ">NASDAQ</a></button>';
	$html .= '</div>';
	$html .= '<div class="pagination">';

	$table = (isset($_GET['table']) && $_GET['table'] === 'NASDAQ') ? 'NASDAQ' : 'NYSE';
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	global $total_pages;

	if ($page > $total_pages) {
		$page = $total_pages;
	}

	$start_page = 1;
	$end_page = 10;

	if ($total_pages > 10) {
		if ($page > 5) {
		$start_page = $page - 5;
		$end_page = $page + 4;
		if ($end_page > $total_pages) {
			$end_page = $total_pages;
			$start_page = max($end_page - 9, 1);
		}
	} else {
		$end_page = min($total_pages, 10);
		}
	} else {
		$end_page = $total_pages;
	}

	for ($i = $start_page; $i <= $end_page; $i++) {
		$active_class = ($i == $page) ? 'active' : '';
		$html .= '<a href="?table=' . $table . '&page=' . $i . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '" class="page-link ' . $active_class . '" data-page="' . $i . '">' . $i . '</a>';
	}

	$html .= '<button class="sort-button"><a href="?table=' . $table . '&sort_by=RSI_PE_PB';
		if (isset($_GET['sort_by']) && $sort_by == 'RSI_PE_PB') {
		if (isset($_GET['sort_order']) && $sort_order == 'desc') {
			$html .= '&sort_order=asc">Sort &#9650;</a></button>';
		} else {
			$html .= '&sort_order=desc">Sort &#9660;</a></button>';
		}
		} else {
			$html .= '">Sort</a></button>';
		}
 
	$html .= '<button class="display-button">Display</button>';
	$html .= '</div>';
	$html .= '<div class="mobileTable">';
	$html .= '<table class="stock-listings-table">';
	$html .= '<thead>';
	$html .= '<tr class="odd">';
	$html .= '<th>Symbol</th>';
	$html .= '<th>Name</th>';
  
	$html .= '<th title="Market Capitalization"><a href="?table=' . $table . '&sort_by=CAP';
	if (isset($_GET['sort_by']) && $sort_by == 'CAP') {
		$html .= '&sort_order=';
		$html .= ($sort_order == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">CAP</a> ';
	if (isset($_GET['sort_by']) && $sort_by == 'CAP') {
		$html .= ($sort_order == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Price–earnings ratio"><a href="?table=' . $table . '&sort_by=PE';
	if (isset($_GET['sort_by']) && $sort_by == 'PE') {
		$html .= '&sort_order=';
		$html .= ($sort_order == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">P/E</a> ';
	if (isset($_GET['sort_by']) && $sort_by == 'PE') {
		$html .= ($sort_order == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="price-to-book ratio"><a href="?table=' . $table . '&sort_by=PB';
	if (isset($_GET['sort_by']) && $sort_by == 'PB') {
		$html .= '&sort_order=';
		$html .= ($sort_order == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">P/B</a> ';
	if (isset($_GET['sort_by']) && $sort_by == 'PB') {
		$html .= ($sort_order == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Relative Strength Index"><a href="?table=' . $table . '&sort_by=RSI';
	if (isset($_GET['sort_by']) && $sort_by == 'RSI') {
		$html .= '&sort_order=';
		$html .= ($sort_order == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">RSI</a> ';
	if (isset($_GET['sort_by']) && $sort_by == 'RSI') {
		$html .= ($sort_order == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Earnings Per Share"><a href="?table=' . $table . '&sort_by=EPS';
	if (isset($_GET['sort_by']) && $sort_by == 'EPS') {
		$html .= '&sort_order=';
		$html .= ($sort_order == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">EPS</a> ';
	if (isset($_GET['sort_by']) && $sort_by == 'EPS') {
		$html .= ($sort_order == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>'; 
	$html .= '<th>NEWS:</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody class="stock-listings-data">';

	// Loop through the data to generate the data rows
	$index = 0;
		foreach ($data as $row) {
			$class = ($index % 2 == 0) ? 'even' : 'odd';
			$html .= '<tr class="' . $class . '">';
			$html .= '<td><button class="symbol-button" data-name="'.htmlspecialchars($row['name'], ENT_QUOTES).'" data-symbol="'.htmlspecialchars($row['symbol'], ENT_QUOTES).'">' . htmlspecialchars($row['symbol'], ENT_QUOTES) . '</button></td>';
			$html .= '<td>' . htmlspecialchars($row['name'], ENT_QUOTES) . '</td>';
			$html .= '<td>' . $row['market_cap_string'] . '</td>';
			$html .= '<td class="modified">' . $row['pe_ratio'] . '</td>';
			$html .= '<td class="modified">' . $row['pb_ratio'] . '</td>';
			$html .= '<td class="modified">' . $row['rsi_analysis'] . '</td>';
			$html .= '<td class="modified">' . $row['eps'] . '</td>';
			$html .= '<td><button class="news-button" news-symbol="'.htmlspecialchars($row['symbol'], ENT_QUOTES).'" news-name="'.htmlspecialchars($row['name'], ENT_QUOTES).'">Read</button></td>';
			$html .= '</tr>';
			$index++;
		}

	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="pagination">';

	$table = (isset($_GET['table']) && $_GET['table'] === 'NASDAQ') ? 'NASDAQ' : 'NYSE';
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

	global $total_pages;

	if ($page > $total_pages) {
		$page = $total_pages;
	}

	$start_page = 1;
	$end_page = 10;

	if ($total_pages > 10) {
		if ($page > 5) {
			$start_page = $page - 5;
			$end_page = $page + 4;
		if ($end_page > $total_pages) {
			$end_page = $total_pages;
			$start_page = max($end_page - 9, 1);
		}
	} else {
		$end_page = min($total_pages, 10);
	}
	} else {
		$end_page = $total_pages;
	}

	for ($i = $start_page; $i <= $end_page; $i++) {
		$active_class = ($i == $page) ? 'active' : '';
		$html .= '<a href="?table=' . $table . '&page=' . $i . '&sort_by=' . $sort_by . '&sort_order=' . $sort_order . '" class="page-link ' . $active_class . '" data-page="' . $i . '">' . $i . '</a>';
	}

	$html .= '</div>';
	return $html;
}

function get_table_html() {
  // Connect to the database
  $conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
  
  // Generate the table data
  $data = generate_table_data($conn, $sector);
  
  // Generate the table HTML
  $html = generate_table_html($data, $sector);
  mysqli_close($conn);
  return $html;
}

?>