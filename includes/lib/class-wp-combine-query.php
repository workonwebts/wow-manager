<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
if( ! class_exists( 'WP_Combine_Queries' ) ) {
/**
 * Class WP_Combine_Queries
 * 
 * @uses WP_Query_Empty
 * @link https://stackoverflow.com/a/23704088/2078474
 *
 */

class WP_Combine_Queries extends WP_Query 
{
    protected $args    = array();
    protected $sub_sql = array();
    protected $sql     = '';

    public function __construct( $args = array() )
    {
        $defaults = array(
            'sublimit'       => 1000,
            'posts_per_page' => 10,
            'paged'          => 1,
            'args'           => array(),
        );

        $this->args = wp_parse_args( $args, $defaults );

        add_filter( 'posts_request',  array( $this, 'posts_request' ), PHP_INT_MAX  );

        parent::__construct( array( 'post_type' => 'post' ) );
    }

    public function posts_request( $request )
    {
        remove_filter( current_filter(), array( $this, __FUNCTION__ ), PHP_INT_MAX  );

        // Collect the generated SQL for each sub-query:
        foreach( (array) $this->args['args'] as $a )
        {
            $q = new WP_Query_Empty( $a, $this->args['sublimit'] );
            $this->sub_sql[] = $q->get_sql();
            unset( $q );
        }

        // Combine all the sub-queries into a single SQL query.
        // We must have at least two subqueries:
        if ( count( $this->sub_sql ) > 1 )
        {
            $s = '(' . join( ') UNION (', $this->sub_sql ) . ' ) ';

            $request = sprintf( "SELECT SQL_CALC_FOUND_ROWS * FROM ( $s ) as combined LIMIT %s,%s",
                $this->args['posts_per_page'] * ( $this->args['paged']-1 ),
                $this->args['posts_per_page']
            );          
        }
        return $request;
    }

} // end class

/**
 * Class WP_Query_Empty
 *
 * @link https://stackoverflow.com/a/23704088/2078474
 */

class WP_Query_Empty extends WP_Query 
{
    protected $args      = array();
    protected $sql       = '';
    protected $limits    = '';
    protected $sublimit  = 0;

    public function __construct( $args = array(), $sublimit = 1000 )
    {
        $this->args     = $args;
        $this->sublimit = $sublimit;

        add_filter( 'posts_clauses',  array( $this, 'posts_clauses' ), PHP_INT_MAX  );
        add_filter( 'posts_request',  array( $this, 'posts_request' ), PHP_INT_MAX  );

        parent::__construct( $args );
    }

    public function posts_request( $request )
    {
        remove_filter( current_filter(), array( $this, __FUNCTION__ ), PHP_INT_MAX );
        $this->sql = $this->modify( $request );             
        return '';
    }

    public function posts_clauses( $clauses )
    {
        remove_filter( current_filter(), array( $this, __FUNCTION__ ), PHP_INT_MAX  );
        $this->limits = $clauses['limits'];
        return $clauses;
    }

    protected function modify( $request )
    {
        $request = str_ireplace( 'SQL_CALC_FOUND_ROWS', '', $request );

        if( $this->sublimit > 0 )
            return str_ireplace( $this->limits, sprintf( 'LIMIT %d', $this->sublimit ), $request );
        else
            return $request;
    }

   public function get_sql( )
    {
        return $this->sql;
    }

} // end class
}
?>