<?php

class Products {

    function __construct($action) {
        
        $available_functions = array(
            'overview' => 'products_overview',
            'add' => 'product_add',
            'delete' => 'product_delete',
        );

        if(!empty($action) && isset($available_functions[$action])) {
            $this->{$available_functions[$action]}();
            return;
        }

        $this->products_overview();
    }

    function products_overview() {
        global $app;

        $products = $app->database->get(array(
            'table' => 'products'
        ));

        $categories = $data = $app->database->get(array(
            'table' => 'categories',
            'where' => 'id_customer =' . $id_customer
        ));

        foreach($products as $key => $product) {
            $category_data = $product['id_category'];
            $products[$key]['category'] = $categories[$category_data];
        }

        $app->page->set_view_data($products);

        $app->page->load_view('product_overview');
    }

    function product_add() {
        global $app;

        $products = array();
        $url_parameters = $app->page->get_url_parameters();

        // Check for parameter id
        if(!empty($url_parameters) && !empty($url_parameters[2])) {
            $id_product = $url_parameters[2];

            $products = $app->database->get(array(
                'table' => 'products',
                'where' => 'id_product = ' . $id_product,
            ));

            $app->page->set_view_data($products);
        }

        // Load template
        $app->page->load_view('products_add');

        if(!isset($_POST['submit'])) {
            return;
        }

        // Save product
        $app->database->save('products', 'id_product', array(
            'id_product' => !empty($products['id_product']) ? $products['id_product'] : '',
            'saved' => !empty($_POST['saved']) ? strtotime($_POST['saved']) : 0,
            'description'=> !empty($_POST['description']) ? $_POST['description'] : '',
            'stock' => !empty($_POST['stock']) ? $_POST['stock'] : 0,
            'comments' => !empty($_POST['comments']) ? $_POST['comments'] : '',
        ));
    }

    function product_delete() {
        global $app;

        $url_parameters = $app->page->get_url_parameters();
        if(empty($url_parameters) || empty($url_parameters[2])) {
            return;
        }

        $app->database->remove('products', 'id_product', $url_parameters[2]);
        
        header("Location: /products/overview");
        return;
    }

}

?>