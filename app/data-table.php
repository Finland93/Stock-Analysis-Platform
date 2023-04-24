<?php
require_once '../app/db-config.php';
$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

	function generate_table_data($conn, $sector = null) {
		$data = array();
		global $total_pages;
		$table = '';
	if (isset($_GET['table']) && $_GET['table'] == 'NASDAQ') {
		$table = 'NASDAQ';
		} else {
		$table = 'NYSE';
		}

	$limit = 30;

	$page = 1;
		if (isset($_GET['page']) && is_numeric($_GET['page'])) {
			$page = (int)$_GET['page'];
		}

		$offset = ($page - 1) * $limit;

		$sql = "SELECT symbol, name, sector, peRatio, market_cap, pbRatio, rsiAnalysis, eps FROM $table";

		$sql_count = "SELECT COUNT(*) as total_rows FROM $table";

	if ($sector) {
		$sql .= " WHERE sector='$sector' AND market_cap != 0";
		$sql_count .= " WHERE sector='$sector' AND market_cap != 0";
	}

	$result_count = mysqli_query($conn, $sql_count);
	$row_count = mysqli_fetch_assoc($result_count);

	$total_rows = $row_count['total_rows'];
	$total_pages = ceil($total_rows / $limit);

	// Check if the user clicked the CAP column
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'CAP') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY $table.market_cap $sort_order";
	}

	// Check if the user clicked the RSI column
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'RSI') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY $table.rsiAnalysis $sort_order";
	}

	// Check if the user clicked the PE column
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PE') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY $table.peRatio $sort_order";
	}

	// Check if the user clicked the PB column
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PB') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY $table.pbRatio $sort_order";
	}

	// Check if the user clicked the EPS column
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'EPS') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY $table.eps $sort_order";
	}

	// Check if the user clicked the sort button
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'RSI_PE_PB') {
		// Determine the sort order
		$sort_order = 'ASC';
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
		$sort_order = 'DESC';
		}
	$sql .= " ORDER BY (rsiAnalysis + peRatio + pbRatio) $sort_order";
	}

	$sql .= " LIMIT $limit OFFSET $offset";
	$result = mysqli_query($conn, $sql);

	if (mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_assoc($result)) {
	//MARKET CAPITALIZATION CLEAN TEXT
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
	);}
	}
	return $data;
}

function generate_table_html($data) {
	$html = '<div class="stock-listings">';
	$html .= '<div class="stock-listings-buttons">';
	$html .= '<button class="nyse-button" data-type="nyse"><a href="?table=NYSE">NYSE</a></button>';
	$html .= '<button class="nasdaq-button" data-type="nasdaq"><a href="?table=NASDAQ">NASDAQ</a></button>';
	$html .= '</div>';
	$html .= '<div class="pagination">';

	$table = isset($_GET['table']) ? $_GET['table'] : '';
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
		$html .= '<a href="?table=' . $table . '&page=' . $i . '&sort_by=' . (isset($_GET['sort_by']) ? $_GET['sort_by'] : '') . '&sort_order=' . (isset($_GET['sort_order']) ? $_GET['sort_order'] : '') . '" class="page-link ' . $active_class . '" data-page="' . $i . '">' . $i . '</a>';
	}

	$html .= '<button class="sort-button"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=RSI_PE_PB';
		if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'RSI_PE_PB') {
		if (isset($_GET['sort_order']) && $_GET['sort_order'] == 'desc') {
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
  
	$html .= '<th title="Market Capitalization"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=CAP';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'CAP') {
		$html .= '&sort_order=';
		$html .= ($_GET['sort_order'] == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">CAP</a> ';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'CAP') {
		$html .= ($_GET['sort_order'] == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Price–earnings ratio"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=PE';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PE') {
		$html .= '&sort_order=';
		$html .= ($_GET['sort_order'] == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">P/E</a> ';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PE') {
		$html .= ($_GET['sort_order'] == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="price-to-book ratio"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=PB';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PB') {
		$html .= '&sort_order=';
		$html .= ($_GET['sort_order'] == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">P/B</a> ';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'PB') {
		$html .= ($_GET['sort_order'] == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Relative Strength Index"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=RSI';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'RSI') {
		$html .= '&sort_order=';
		$html .= ($_GET['sort_order'] == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">RSI</a> ';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'RSI') {
		$html .= ($_GET['sort_order'] == 'asc') ? '&#9650;' : '&#9660;';
	}
	$html .= '</th>';
  
	$html .= '<th title="Earnings Per Share"><a href="?table=' . (isset($_GET['table']) ? $_GET['table'] : '') . '&sort_by=EPS';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'EPS') {
		$html .= '&sort_order=';
		$html .= ($_GET['sort_order'] == 'asc') ? 'desc' : 'asc';
	} else {
		$html .= '&sort_order=asc';
	}
	$html .= '">EPS</a> ';
	if (isset($_GET['sort_by']) && $_GET['sort_by'] == 'EPS') {
		$html .= ($_GET['sort_order'] == 'asc') ? '&#9650;' : '&#9660;';
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
			$html .= '<td><button class="symbol-button" data-name="'.$row['name'].'" data-symbol="'.$row['symbol'].'">' . $row['symbol'] . '</button></td>';
			$html .= '<td>' . $row['name'] . '</td>';
			$html .= '<td>' . $row['market_cap_string'] . '</td>';
			$html .= '<td class="modified">' . $row['pe_ratio'] . '</td>';
			$html .= '<td class="modified">' . $row['pb_ratio'] . '</td>';
			$html .= '<td class="modified">' . $row['rsi_analysis'] . '</td>';
			$html .= '<td class="modified">' . $row['eps'] . '</td>';
			$html .= '<td><button class="news-button" news-symbol="'.$row['symbol'].'" news-name="'.$row['name'].'">Read</button></td>';
			$html .= '</tr>';
			$index++;
		}

	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="pagination">';

	$table = isset($_GET['table']) ? $_GET['table'] : '';
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
		$html .= '<a href="?table=' . $table . '&page=' . $i . '&sort_by=' . (isset($_GET['sort_by']) ? $_GET['sort_by'] : '') . '&sort_order=' . (isset($_GET['sort_order']) ? $_GET['sort_order'] : '') . '" class="page-link ' . $active_class . '" data-page="' . $i . '">' . $i . '</a>';
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