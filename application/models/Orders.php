<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        $CI = &get_instance();
        if ($CI->orderitems->exists($num, $code))
        {
            $record = $CI->orderitems->get($num, $code);
            $record->quantity++;
            $CI->orderitems->update($record);
        } else {
            $record = $CI->orderitems->create();
            $record->num = $num;
            $record->item = $code;
            $record->quantity = 1;
            $CI->orderitems->add($record);
        }
    }

    // calculate the total for an order
    function total($num) {
        // Access orderitems model
        $CI = &get_instance();
        $CI->load->model('orderitems');

        // All items in the current order
        $items = $this->orderitems->some('code', $num);

        // Add prices
        $sum = 0;
        foreach ($items as $item)
        {
            $menuitem = $this->menu->get($item->item);
            $sum += $item->quantity * $menuitem->price;
        }

        return $sum;
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {

        $CI = &get_instance();
        $items = $CI->orderitems->group($num);
        $gotem = array();

        if (count($items) > 0)
            foreach ($items as $item) {
                $menu = $CI->menu->get( $item->item );
                $gotem[$menu->category] = 1;
            }

        return isset($gotem['m']) && isset($gotem['d']) && isset($gotem['s']);
    }

}
