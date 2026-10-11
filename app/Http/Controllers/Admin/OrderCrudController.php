<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OrderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrderCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Order::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/order');
        CRUD::setEntityNameStrings('order', 'orders');
        CRUD::addColumn(
            [
                'name'  => 'delivery_date', // The db column name
                'label' => 'delivery_date', // Table column heading
                // 'type'  => 'array'
                'visibleInTable'  => false, // no point, since it's a large text
                'visibleInModal'  => false, // would make the modal too big
                'visibleInExport' => false, // not important enough
                'visibleInShow'   => false, // sure, why not
            ],
        );
        CRUD::addColumn(
            [
                'name'  => 'pickup_date', // The db column name
                'label' => 'pickup_date', // Table column heading
                // 'type'  => 'array'
                'visibleInTable'  => false, // no point, since it's a large text
                'visibleInModal'  => false, // would make the modal too big
                'visibleInExport' => false, // not important enough
                'visibleInShow'   => false, // sure, why not
            ],
        );
    }





    // protected function fetchTag()
    // {
    //     return $this->fetch(\App\Models\Customer::class);
    // }



    public function import()
    {
        return "okkkk";
        // whatever you decide to do
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // columns
        $this->crud->column('id');
        CRUD::addColumn(
            [
                'name'      => 'customer', // name of relationship method in the model
                'type'      => 'relationship',
                'attribute'=>'first_name',
                'label'     => 'fname', // Table column heading
                'model'     => 'App\Customer',

            ],
            [
                'name'      => 'customer', // name of relationship method in the model
                'type'      => 'relationship',
                'attribute'=>'last_name',
                'key'       => 'customer_last_name',
                'label'     => 'lname', // Table column heading
                'model'     => 'App\Customer',

            ],
        );
        // CRUD::addColumn(
        //     [
        //         'name'      => 'customer', // name of relationship method in the model
        //         'type'      => 'relationship',
        //         'attribute'=>'last_name',
        //         'key'       => 'customer_last_name',
        //         'label'     => 'lname', // Table column heading
        //         'model'     => 'App\Customer',

        //     ],
        // );
        CRUD::addColumn(
            [
                'name'      => 'Promotion', // name of relationship method in the model
                'type'      => 'relationship',
                'label'     => 'Promotion', // Table column heading
                'attribute'=>'moha'

            ],
        );
        $this->crud->addButtonFromView('line', 'moderate', 'moderate', 'end');
        $this->crud->addButtonFromModelFunction('line', 'open_google', 'openGoogle', 'end');
        $this->crud->addButtonFromView('top', 'import', 'import', 'end');
        // CRUD::addColumn(
        //     [
        //         'name'  => 'pickup_date', // The db column name
        //         'label' => 'pickup_date', // Table column heading

        //     ],
        // );
        // CRUD::addColumn(
        //     [
        //         'name'      => 'customers', // name of relationship method in the model
        //         // 'type'      => 'relationship_count',
        //         'label'     => 'Tags', // Table column heading
        //     ],
        // );
        // CRUD::addColumn(
        //     [
        //         'name'  => 'delivery_date', // The db column name
        //         'label' => 'delivery_date', // Table column heading

        //     ],
        // );
        // CRUD::addColumn(
        //     [
        //         // 1-n relationship
        //         'label'     => 'customer', // Table column heading
        //         'type'      => 'select',
        //         'name'      => 'customer_id', // the column that contains the ID of that connected entity;
        //         'entity'    => 'customer', // the method that defines the relationship in your Model
        //         'attribute' => 'first_name', // foreign key attribute that is shown to user
        //         'model'     => "App\Models\Customer", // foreign key model
        //     ],
        // );
        // CRUD::addColumn(
        //     [
        //         'name'     => 'created_at',
        //         'label'    => 'Created At',
        //         'type'     => 'closure',
        //         'function' => function ($entry) {
        //             return 'Created on ' . $entry->created_at;
        //         }
        //     ],
        // );


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupShowOperation()
    {
        //     CRUD::addColumn(
        //     [
        //         'name'  => 'delivery_date', // The db column name
        //         'label' => 'delivery_date', // Table column heading

        //     ],
        // );
    }
    protected function setupCreateOperation()
    {
        CRUD::setValidation(OrderRequest::class);

        // CRUD::setFromDb(); // fields
        CRUD::addField([
            'type' => "relationship",
            'name' => 'customer_id', // the method on your model that defines the relationship
            // 'ajax' => true,
            'inline_create' => true, // assumes the URL will be "/admin/category/inline/create"
        ]);

        // CRUD::addField(
        //     [
        //         // 1-n relationship
        //         'label'     => 'customer', // Table column heading
        //         'type'      => 'select  ',
        //         'name'      => 'customer_id', // the column that contains the ID of that connected entity;
        //         'entity'    => 'customer', // the method that defines the relationship in your Model
        //         'attribute' => 'first_name', // foreign key attribute that is shown to user
        //         'model'     => "App\Models\Customer", // foreign key model
        //     ],
        //     // [
        //     //     'name'  => 'pickup_date', // The db column name
        //     //     'label' => 'pickup_date', // Table column heading
        //     //     // 'type'  => 'array'
        //     // ],
        // );

        CRUD::addField(
            [
                'name'  => 'pickup_date', // The db column name
                'label' => 'pickup_date', // Table column heading
                // 'type'  => 'array'
            ],
        );
        CRUD::addField(
            [
                // select_from_array
                'name'    => 'method_of_payment',
                'label'   => 'payment method',
                'type'    => 'select_from_array',
                'options' => ['cash' => 'cash', 'card' => 'Card (Credit)'],
            ],
        );

        CRUD::addField(
            [
                'name'  => 'method_of_payment', // The db column name
                'label' => 'method_of_payment', // Table column heading
                // 'type'  => 'array'
            ],
        );

        CRUD::addField(['name' => 'pickup_start']);
        CRUD::addField(['name' => 'pickup_end']);

        CRUD::addField(['name' => 'delivery_date']);
        CRUD::addField(['name' => 'delivery_start']);
        CRUD::addField(['name' => 'delivery_end']);
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }


    public function moderate()
    {
        return view("base.blank");
        // show a form that does something
    }
}
