<?php 
// Se non esiste la classe eseguo il caricamento
// del file di wordpress necessario all'esecuzione

if (!class_exists('WP_List_Table')) {
 require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

// Definizione Classe per il nostro database e aggiunta
// dei metodi necessari alla visualizzazione delle informazioni
// versione: 1.1

if (!class_exists('WoW_AdminTable')) {
class WoW_AdminTable extends WP_List_Table {
	public $appRef;
	/**
	* The textdomain if exist
	* @var 	string
	* @access  public
	* @since 	1.0.0
	*/
	public $textdomain;
	private $table;
	private $table_name;
	private $title;
	private $columns;
	private $columns_hidden;
	private $columns_sortable;
	private $item_per_page=25;
	private $fld_select='';
	private $fld_order='';
	private $use_prefix;
	private $_my_actions;
	private $_bulk_actions;
	private $fld_search;
	private $single_table;
	private $sql;
	
	
  function __construct($aTableParam=array(), $appRef=NULL) {
		if (is_null($appRef)) {
			$this->appRef=$this;
		} else {
			$this->appRef=$appRef;
		}
		if(isset($this->appRef->textdomain)) {
			$this->textdomain=$this->appRef->textdomain;
		} else {
			$this->textdomain=sanitize_file_name(basename(__FILE__,'.php'));
		}
		$this->table='';
		$this->use_prefix=false;
		$this->columns=array();
		$this->columns_hidden=array();
		$this->columns_sortable=array();
		$this->_my_actions=array();
		$this->_bulk_actions=array();
		$this->fld_search=array();
		$this->title=$aTableParam['plural'];
		$this->single_table=true;
		$this->table_name = '';
		$this->sql=array();
//		$this->set_sql_basic();
/* parametri construct
    $args = array(
        'plural' => '',
        'singular' => '',
        'ajax' => false,
        'screen' => null,
    );
*/
	  $aTableP=array(
		'plural' => 'Generics',
		'singular' => 'Generic',
		'ajax'	=> false
	  );
	  $aTableParam=array_merge($aTableP,$aTableParam);
	  parent::__construct($aTableParam);
  }
    /**
     * Initialize the class and start calling our hooks and filters
     */
	public function init($table,$bpre) {
		$this->set_prefix($bpre);
		$this->set_table($table);
		$this->set_sql_basic();
	}

	public function set_table($tblname=''){
		if ($tblname=='') die("Impostare il nome della tabella principale: impossible continuare!");
		$this->table=$tblname;
		$this->set_tablename();
	}
	public function get_table(){
		return $this->table;
	}
	public function set_tablename(){
	  global $wpdb;
		$this->table_name = ($this->get_prefix()?$wpdb->prefix.$this->get_table():$this->get_table());
	}
	public function get_tablename(){
		return $this->table_name;
	}
	public function get_prefix() {
		return $this->use_prefix;
	}
	public function set_prefix($bprefix=true) {
		$this->use_prefix=$bprefix;
	}
	// funzione per restituire il nome pagina per l'intestazione
	public function get_title() {
		return $this->title;
	}
	// funzione per impostare il nome pagina per l'intestazione
	public function set_title($pagina='') {
		if ($pagina=='') return false;
		$this->title=$pagina;
		return true;
	}
	
	/**
	* Checks the current user's permissions
	*
	* @since 3.1.0
	* @abstract
	*/
	public function ajax_user_can() {
		//die( 'function WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
		return true;
	}

  // Funzione per la preparazione dei campi da visualizzare
  // e la query SQL principale che deve essere eseguita 

// Aggiungete il codice del navigatore alla funzione esistente
// prepare_items() che abbiamo definito nella classe precedente

	function prepare_items() {
		global $wpdb;
		$table_name = $this->table_name;
		$per_page = $this->item_per_page; // Numero dei record presenti in una pagina
		
		// Calcolo elenco de dei campi per le differenti
		// sezioni e memorizzo tutto in array separati
		
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		
		// Bisogna memorizzare tre array che devono contenere i campi da 
		// visualizzare, quelli nascosti e quelli per eseguire l'ordinamento
		
		$this->_column_headers = array($columns,$hidden,$sortable);
		
		// Preparazione delle variabili che devono essere utilizzate
		// nella preparazione della query con gli ordinamenti e la posizione
		
		if (!isset($_REQUEST['paged'])) $paged = 0;
		  else $paged = max(0,(intval($_REQUEST['paged'])-1)*$per_page);
		
		if (isset($_REQUEST['orderby'])
			and in_array($_REQUEST['orderby'],array_keys($sortable)))
		$orderby = $_REQUEST['orderby']; else $orderby = $this->get_fld_order();
		
		if (isset($_REQUEST['order'])
			and in_array($_REQUEST['order'],array('asc','desc')))
		$order = $_REQUEST['order']; else $order = 'asc';
		
		// Calcolo le variabili che contengono il numero dei record totali
		// e l'elenco dei record da visualizzare per una singola pagina
		$is_no_w=(''==$this->get_sql_item('where'));
		if ($is_no_w) {
			$WHERE = "WHERE 1";
		} else {
			$WHERE = sprintf( "WHERE 1 and ( %s )", $this->get_sql_item('where'));
		}
		if (isset($_REQUEST['s']) and !empty($_REQUEST['s'])) 	{
			$asearch=array();
			foreach ($this->get_fld_search() as $src) $asearch[]=sprintf("%s LIKE '%%%s%%'",$src,$_REQUEST['s']);
			$search=implode(" OR ", $asearch);
			$WHERE .= 'and ('.sprintf( " and ( %s )", $search).')';
		}
		$query = sprintf("SELECT %s FROM %s %s %s", $this->get_sql_item('select'), $this->get_sql_item('from'), $this->get_sql_item('join'), $WHERE);
		$query_c = sprintf("SELECT %s FROM %s %s %s", 'count(*)', $this->get_sql_item('from'), $this->get_sql_item('join'), $WHERE);
		
		$total_items = $wpdb->get_var($query_c);
		$this->sql['qcont']=$query_c;
		$query=sprintf("%s ORDER BY %s %s LIMIT %d OFFSET %d", $query, $orderby, $order, $per_page, $paged);
		$this->sql['query']=$query;
		$this->items = $wpdb->get_results($query,ARRAY_A);

		// paginazione		
		$this->set_pagination_args(array(
		'total_items' => $total_items,
		'per_page'    => $per_page,
		'total_pages' => ceil($total_items/$per_page)
		));
	}
	// funzione per abilitare query su singola tabella
		public function set_single_table() {
		$this->single_table=true;
	}
	// funzione per abilitare query su più tabelle
		public function set_multi_table() {
		$this->single_table=false;
	}
	// funzione restituisce se query single/multi table
		public function get_single_table() {
		return $this->single_table;
	}
	// funzione per restituire tutti i valori di $sql
	public function get_sql() {
		return $this->sql;
	}
	// funzione per restituire i componenti sql
	public function get_sql_item($clause) {
		return (isset($this->sql[$clause])?$this->sql[$clause]:'');
	}
	// funzione per creare sql standard 
	public function set_sql_basic() {
		$this->sql['select']='*';
		$this->sql['from']=$this->table_name;
		$this->sql['join']='';
		$this->sql['where']='';
		$this->sql['query']='';
		$this->sql['qcont']='';
	}
	// funzione per aggiungere i campi in SELECT della query
	public function set_sql_select($flds='') {
		$this->sql['select']=$flds;
	}
	// funzione per aggiungere clausola FROM alla query
	public function set_sql_from($flds='') {
		if ($this->get_single_table()) {
			$this->sql['from']=$this->table_name;
		} else {
			$this->sql['from']=$flds;
		}
	}
	// funzione per aggiungere clausola JOIN alla query
	public function set_sql_join($flds='') {
		if ($this->get_single_table()) {
			$this->sql['join']='';
		} else {
			$this->sql['join']=$flds;
		}
	}
	// funzione per aggiungere clausola WHERE alla query
	public function set_sql_where($flds='') {
		if ($this->get_single_table()) {
			$this->sql['where']='';
		} else {
			$this->sql['where']=$flds;
		}
	}
	// funzione per impostare il campo chiave per selezione  e ordinamento
	public function set_fld_order($fld_ord='') {
		if ($fld_ord=='') return false;
		$this->fld_order=$fld_ord;
		return true;
	}
	public function get_fld_order() {
		return $this->fld_order;
	}
  
	// funzione per impostare il campo chiave per selezione  e ordinamento
	public function set_fld_select($fld_sel='') {
		if ($fld_sel=='') return false;
		$this->fld_select=$fld_sel;
		return true;
	}
	public function get_fld_select() {
		return $this->fld_select;
	}
  
	// funzione per impostare il numero di record per pagina
	public function set_item_per_page($n_row=0) {
		if ($n_row==0) return false;
		$this->item_per_page=$n_row;
		return true;
	}
	// funzione per visualizzare il numero di record per pagina
	public function get_item_per_page() {
		if ($n_row==0) return false;
		return $this->item_per_page;
	}
	// funzione per aggiungere il campo checkbox per la selezione
	public function add_select_column($fld_sel='') {
		if ($fld_sel=='') return false;
		$new_fld=array();
		$new_fld['cb']='<input type="checkbox"/>';
		foreach($this->columns as $k=>$v) {
			$new_fld[$k]=$v;
		}
		$this->set_fld_select($fld_sel);
		$this->columns=$new_fld;
		return $this->columns;
	}
	// funzione per aggiungere i campi da visualizzare
	public function add_column($fld_name='', $fld_label='', $is_sort=false, $fld_desc=false, $is_ord_main=false) {
		if ($fld_name=='' || $fld_label=='') return false;
		$this->columns[$fld_name]=$fld_label;
		if ($is_sort) $this->add_sortable_column($fld_name, $fld_desc);
		if ($is_ord_main) $this->set_fld_order($fld_name);
		add_filter( "wow_admintable_column_{$fld_name}_value", array($this,'get_column_value'), 10, 3 );
		return true;
	}

	// Funzione per la definizione dei campi che devono
	// essere visualizzati nella lista da visualizzare
	function get_columns()   {
	  /*
	$columns = array(
	  'tessera'            => 'Tessera',
	  'cognome'            => 'Nominativo',
	  'datadinascita'      => 'Nato il',
	  'indirizzo'          => 'Indirizzo',
	  'localita'           => 'Località',
	  'provincia'          => 'PR',
	  'spesatotale'        => 'Totale',
	  'totalescontrini'    => 'Scontrini',
	  'spesamedia'         => 'Media',
	  'puntiattuali'       => 'Punti',
	  'dataprimoacquisto'  => 'Acquisto',
	  'dataultimoacquisto' => 'Ultimo',
	);*/
		return $this->columns;
	}

	// funzione per aggiungere campi per ordinare la tabella
	public function add_sortable_column($fld_name='', $fld_desc=false) {
		if ($fld_name=='') return false;
		$this->columns_sortable[$fld_name]=array($fld_name,$fld_desc);
		return true;
	}
	// Funzione per la definizione dei campi che possono
	// essere utilizzati per eseguire la funzione di ordinamento
	
	function get_sortable_columns() {
	  /*
	$sortable_columns = array(
	  'tessera'       => array('tessera',true),
	  'cognome'       => array('cognome',true),
	  'datadinascita' => array('datadinascita',false),
	  'spesatotale'   => array('spesatotale',false),
	  'puntiattuali'  => array('puntiattuali',false),
	);*/
		return $this->columns_sortable;
	}
	// funzione per aggiungere campi nascosti della tabella
	public function add_hidden_column($fld_name='') {
		if ($fld_name=='') return false;
		$this->columns_hidden[$fld_name]=$fld_name;
		return true;
	}

	// Funzione per la definizione dei campi che devono 
	// essere calcolati dalla query ma non visualizzati
	
	function get_hidden_columns() {
		return $this->columns_hidden;
	}

	// funzione per aggiungere le actions
	public function add_action($act, $desc) {
		$this->_my_actions[$act]=$desc;
	}
	public function get_actions() {
		return $this->_my_actions;
	}
	// funzione per aggiungere le bulk actions
	public function add_bulk_action($act, $desc) {
		$this->_bulk_actions[$act]=$desc;
	}
	// Definire la nuova funzione per indicare le
	// azioni che devo essere presenti sul menu a tendina
	function get_bulk_actions() {
	  return $this->_bulk_actions;
	}
	// funzione per aggiungere i campi x search
	public function add_fld_search($fld='') {
		if ($fld=='') return false;
		$this->fld_search[]=$fld;
		return true;
	}
	public function get_fld_search() {
		return $this->fld_search;
	}
	
	// Visualizzazione Tabella
	public function view_page() {
	
		// Definizione variabili per contenere i valori
		// di paginazione e il nome della pagina visualizzata
		$page = filter_input(INPUT_GET,'page' ,FILTER_SANITIZE_STRIPPED);
		$paged = filter_input(INPUT_GET,'paged',FILTER_SANITIZE_NUMBER_INT);
		
		echo '<div class="wrap">';
		echo '<h2>'.$this->get_title().'</h2>';
		// azione per inserire form di filtro dati
		do_action('wow_admintableview_paging_hidden_fields');

		// Form di ricerca da aggiungere prima della tabella se prevista
		// indicare i campi hidden che si vogliono conservare
		if (count($this->get_fld_search()) > 0) {
			echo '<form method="get">';
			echo '<input type="hidden" name="page" value="'.$page. '"/>';
			do_action('wow_admintableview_search_hidden_fields');
			$this->search_box('Search','search_id');
			echo '</form>';
		}
		// Form per contenere la tabella con elenco records
		// presenti nel database e campi definiti nella classe
		echo '<form id="persons-table" method="GET">';
		echo '<input type="hidden" name="paged" value="'.$paged.'"/>';
		do_action('wow_admintableview_paging_hidden_fields');
		$this->display(); // Metodo per visualizzare elenco records
		echo '</form>';
		
		echo '</div>';
/*
		debug_var($this);
		$f=$GLOBALS['wp_filter'];
		foreach($f as $k=>$v) {
			debug_var(is_wow($v,$k,'wow_admintable'));
		}
*/
	}

	// Funzione per reperire il valore di un campo in
	// maniera standard senza una personalizzazione di output
	function get_column_value($value, $item, $column_name) {
		return $value;
	}
	
	function column_default($item,$column_name) { 
		reset($item);
		$first=key($item);
		$sel=$this->get_fld_select();

		// Definizioni azioni che devo comparire sotto il
		// primo campo quando andiamo in hover con il mouse
		// Ritorno  il valore della colonna e
		// richiamo il metodo row_actions per le azioni se ci sono
		if ($sel!='' && $first==$column_name) {
			$actions=array();
			foreach($this->get_actions() as $k=>$v) {
				$actions[$k] = sprintf('<a href="?page=%s&action=%s&%s=%s">%s</a>',	$_REQUEST['page'], $k, $sel, $item[$sel], $v);
			}
			return sprintf('%1$s %2$s',apply_filters("wow_admintable_column_{$column_name}_value",$item[$column_name],$item,$column_name), apply_filters("wow_admintable_column_{$column_name}_actions",$this->row_actions($actions),$this->get_actions(),$item[$column_name],$item,$column_name) );
		} else {
			// imposta il primo campo come contenuto
	//	return $item->$column_name]; 
	//	return $item[$column_name]; 
			return sprintf('%1$s',apply_filters("wow_admintable_column_{$column_name}_value",$item[$column_name],$item,$column_name));
		}
	}

	// Funzione per la prima colonna che non sarà più il 
	// numero di tessera ma un campo di checkbox per la selezione
	
	function column_cb($item) {
		$s=$this->get_fld_select();
		if ($s=='') return $s; 
	  return sprintf(
		'<input type="checkbox" name="%s[]" value="%s"/>',$s, $item[$s]);
	}
/*
  // Dato che alcuni campi hanno bisogno di output 
  // personalizzato bisogna creare una funzione per campo
  // da implementare nella classe derivata

  function column_datadinascita($item) {
    return $this->get_date_format($item['datadinascita']);
  }
*/
}
}

?>