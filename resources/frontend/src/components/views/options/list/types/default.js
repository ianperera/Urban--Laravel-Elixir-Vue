import parseDates from 'src/components/views/_base/ListItems/_helpers/parse-route-search-dates'

let dimensions = [
    {
        name: 'Option ID',
        id: 'id',
        checked: true,
        order_by: 'id',
        query: {
            fields: ['id'],
            group_by: ['id']
        }
    },
    {
        name: 'Category',
        id: 'category_id',
        checked: true,
        order_by: 'category_id',
        query: {
            fields: ['category_id'],
            include: ['category'],
            leftJoinRelation: ['category'],
            group_by: ['category_id']
        }
    },
    {
        name: 'Name',
        id: 'name',
        checked: true,
        order_by: 'name',
        query: {
            fields: ['name'],
            group_by: ['name']
        }
    },
    {
        name: 'Description',
        id: 'description',
        checked: true,
        order_by: 'description',
        query: {
            fields: ['description']
        }
    },
    {
        name: 'Unit Price',
        id: 'unit_price',
        checked: true,
        order_by: 's_unit_price',
        query: {
            fn: {
                'sum.s_unit_price': 'unit_price'
            }
        }
    },
    {
        name: 'Is Active',
        id: 'is_active',
        checked: true,
        order_by: 'is_active',
        query: {
            fields: ['is_active'],
            group_by: ['is_active']
        }
    },
    {
        name: 'Force Qty',
        id: 'force_qty',
        checked: true,
        order_by: 'force_quantity',
        query: {
            fields: ['force_quantity', 'constraint_type'],
            group_by: ['force_quantity']
        }
    },
    {
        name: 'Date Created',
        id: 'date_created',
        checked: true,
        order_by: 'created_at',
        query: {
            fields: ['created_at'],
            group_by: ['created_at']
        }
    }
]

let searches = [
    {
        name: 'Date Created',
        id: 'date_created',
        parse(value) {
            let between = parseDates.ranges(_.get(value, 'between'), 'YYYY-MM-DD HH:mm:ss')
            return {between}
        },
        checked: false,
        query: {
            where: 'created_at'
        }
    },
    {
        name: 'Is Active',
        id: 'is_active',
        value: null,
        checked: false
        // query: {
        //     where: 'is_active'
        // }
    },
    {
        name: 'Category',
        id: 'category_id',
        value: null,
        checked: false
        // query: {
        //     where: 'category_id'
        // }
    }
]

let totals = [
    {
        name: 'Rows',
        id: 'totalRows',
        type: 'unit',
        checked: null,
        selectable: false,
        value: null
    }
]

export default {
    dimensions,
    searches,
    totals
}
