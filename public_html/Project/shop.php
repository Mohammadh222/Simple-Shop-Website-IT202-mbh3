<?php
require(__DIR__ . "/../../partials/nav.php");

$results = [];
$db = getDB();
//Sort and Filters
$col = se($_GET, "col", "cost", false);
//allowed list
if (!in_array($col, ["cost", "stock", "name", "created", "out_stock", "average_rating", "category"])) {
    $col = "cost"; //default value, prevent sql injection
}
$order = se($_GET, "order", "asc", false);
//allowed list
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc"; //default value, prevent sql injection
}
$name = se($_GET, "name", "", false);

//split query into data and total
$base_query = "SELECT id, name, description, cost, stock, image FROM Products";
$total_query = "SELECT count(1) as total FROM Products";


//dynamic query
$query = " WHERE 1=1"; //1=1 shortcut to conditionally build AND clauses
$params = []; //define default params, add keys as needed and pass to execute
//apply name filter
if (!empty($name)) {
    $query .= " AND name like :name";
    $params[":name"] = "%$name%";
}
//apply column and order sort
if (!empty($col) && !empty($order) && $col != "out_stock") {
    $query .= " ORDER BY $col $order"; //be sure you trust these values, I validate via the in_array checks above
}
if (!empty($col) && !empty($order) && $col=="out_stock") {
    $query = " WHERE stock = 0"; //be sure you trust these values, I validate via the in_array checks above
}
//paginate function
$per_page = 3;
paginate($total_query . $query, $params, $per_page);

$query .= " LIMIT :offset, :count";
$params[":offset"] = $offset;
$params[":count"] = $per_page;
//get the records
$stmt = $db->prepare($base_query . $query); //dynamically generated query
//we'll want to convert this to use bindValue so ensure they're integers so lets map our array
foreach ($params as $key => $value) {
    $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $value, $type);
}
$params = null; //set it to null to avoid issues

    $stmt = $db->prepare("SELECT id, name, cost, description FROM Products WHERE stock > 0 LIMIT 50");

try {

    $stmt->execute(); 
    
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $results = $r;
        }
    } catch (PDOException $e) {
        flash("<pre>" . var_export($e, true) . "</pre>");
    }
?>
<script>
    function cart(item, cost) {
        console.log("TODO purchase item", item);
        let example = 1;
        if (example === 1) {
            let http = new XMLHttpRequest();
            http.onreadystatechange = () => {
                if (http.readyState == 4) {
                    if (http.status === 200) {
                        let data = JSON.parse(http.responseText);
                        console.log("received data", data);
                        flash(data.message, "success");
                        /* refreshBalance(); */
                    }
                    console.log(http);
                }
            }
            http.open("POST", "api/purchase_item.php", true);
            let data = {
                item_id: item,
                quantity: 1,
                cost: cost
            }
            let q = Object.keys(data).map(key => key + '=' + data[key]).join('&');
            console.log(q)
            http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            http.send(q);
        } else if (example === 2) {
            let data = new FormData();
            data.append("item_id", item);
            data.append("quantity", 1);
            data.append("cost", cost);
            fetch("api/purchase_item.php", {
                    method: "POST",
                    headers: {
                        "Content-type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    flash(data.message, "success");
                    //refreshBalance();
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        } else if (example === 3) {
            $.post("api/puchase_item.php", {
                    item_id: item,
                    quantity: 1,
                    cost: cost
                }, (resp, status, xhr) => {
                    console.log(resp, status, xhr);
                    let data = JSON.parse(resp);
                    flash(data.message, "success");
                    //refreshBalance();
                },
                (xhr, status, error) => {
                    console.log(xhr, status, error);
                });
        }
        //TODO create JS helper to update all show-balance elements
    }

    

    $stmt = $db->prepare("SELECT id, name, cost, description FROM Products2 WHERE stock > 0 LIMIT 50");

try {

    $stmt->execute(); 
    
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($r) {
            $results = $r;
        }
    } catch (PDOException $e) {
        flash("<pre>" . var_export($e, true) . "</pre>");
    }

</script>

<div class="container-fluid">
    <h1>Shop</h1>
    <form class="row row-cols-auto g-3 align-items-center">
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Name</div>
                <input class="form-control" name="name" value="<?php se($name); ?>" />
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Sort</div>
                <!-- make sure these match the in_array filter above-->
                <select class="form-control" name="col" value="<?php se($col); ?>">
                    <option value="cost">Cost</option>
                    <option value="category">Category</option>
                    <option value="stock">Stock</option>
                    <option value="name">Name</option>
                    <option value="created">Created</option>
                    <option value="average_rating">Average Rating</option>
                    <?php if (has_role("Admin")) : ?>
                        <option value="out_stock">Out of Stock</option>
                    <?php endif; ?>
                </select>
                <script>
                    //quick fix to ensure proper value is selected since
                    //value setting only works after the options are defined and php has the value set prior
                    document.forms[0].col.value = "<?php se($col); ?>";
                </script>
                <select class="form-control" name="order" value="<?php se($order); ?>">
                    <option value="asc">Up</option>
                    <option value="desc">Down</option>
                </select>
                <script>
                    //quick fix to ensure proper value is selected since
                    //value setting only works after the options are defined and php has the value set prior
                    document.forms[0].order.value = "<?php se($order); ?>";
                </script>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <input type="submit" class="btn btn-primary" value="Apply" />
            </div>

        </div>
    </form>

    <img src="products.jpg" alt="product" width="300" height="200">
    <img src="product2.jpg" alt="product" width="300" height="200">
    <img src="product1.jpg" alt="product" width="300" height="200">
    <img src="product3.jpg" alt="product" width="300" height="200">
    <img src="product4.jpg" alt="product" width="300" height="200">
    <img src="product5.jpg" alt="product" width="300" height="200">
    <img src="product6.jpg" alt="product" width="300" height="200">
    <img src="product7.jpg" alt="product" width="300" height="200">

    <div class="row row-cols-1 row-cols-md-5 g-4">
        <?php foreach ($results as $item) : ?>
            <div class="col">
                <div class="card bg-dark">
                    <div class="card-header">
                        
                    </div>
                    <?php if (se($item, "image", "", false)) : ?>
                        <img src="<?php se($item, "image"); ?>" class="card-img-top" alt="...">
                    <?php endif; ?>

                    <div class="card-body">
                    <img src="product2.jpg" alt="product" width="300" height="200">

                        <h5 class="card-title">Name: <?php se($item, "name"); ?></h5>
                        <p class="card-text">Description: <?php se($item, "description"); ?></p>
                        <p  style="margin-bottom: 0px;"><u> </u><?php se($item, "average_rating"); ?></p>
                    </div>
                    <div class="card-footer">
                        Cost: <?php se($item, "cost"); ?>
                        <!-- Cart-->
                        <button onclick="cart('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">Add to Cart</button>
                        <?php if (has_role("Admin")) : ?> 
                            <td>
                                <a href="admin/edit_item.php?id=<?php se($item, "id"); ?>">Edit</a>
                            </td>
                            <?php endif; ?>
                        <!-- example form submit-->
                        <form action="product_details.php" method="PUT">
                            <input type="hidden" name="id" value="<?php se($item, 'id'); ?>" />
                            <button onclick="details('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">product details</Details></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row row-cols-1 row-cols-md-5 g-4">
        <?php foreach ($results as $item) : ?>
            <div class="col">
                <div class="card bg-dark">
                    <div class="card-header">
                        
                    </div>
                    <?php if (se($item, "image", "", false)) : ?>
                        <img src="<?php se($item, "image"); ?>" class="card-img-top" alt="...">
                    <?php endif; ?>

                    <div class="card-body">
                    <img src="product7.jpg" alt="product" width="300" height="200">

                        <h5 class="card-title">Name: <?php se($item, "name"); ?></h5>
                        <p class="card-text">Description: <?php se($item, "description"); ?></p>
                        <p  style="margin-bottom: 0px;"><u> </u><?php se($item, "average_rating"); ?></p>
                    </div>
                    <div class="card-footer">
                        Cost: <?php se($item, "cost"); ?>
                        <!-- Cart-->
                        <button onclick="cart('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">Add to Cart</button>
                        <?php if (has_role("Admin")) : ?> 
                            <td>
                                <a href="admin/edit_item.php?id=<?php se($item, "id"); ?>">Edit</a>
                            </td>
                            <?php endif; ?>
                        <!-- example form submit-->
                        <form action="product_details.php" method="PUT">
                            <input type="hidden" name="id" value="<?php se($item, 'id'); ?>" />
                            <button onclick="details('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">product details</Details></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="row row-cols-1 row-cols-md-5 g-4">
        <?php foreach ($results as $item) : ?>
            <div class="col">
                <div class="card bg-dark">
                    <div class="card-header">
                    </div>
                    <?php if (se($item, "image", "", false)) : ?>
                        <img src="<?php se($item, "image"); ?>" class="card-img-top" alt="...">
                    <?php endif; ?>

                    <div class="card-body">
                    <img src="product4.jpg" alt="product" width="300" height="200">

                        <h5 class="card-title">Name: <?php se($item, "name"); ?></h5>
                        <p class="card-text">Description: <?php se($item, "description"); ?></p>
                        <p  style="margin-bottom: 0px;"><u> </u><?php se($item, "average_rating"); ?></p>
                    </div>
                    <div class="card-footer">
                        Cost: <?php se($item, "cost"); ?>
                        <!-- Cart-->
                        <button onclick="cart('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">Add to Cart</button>
                        <?php if (has_role("Admin")) : ?> 
                            <td>
                                <a href="admin/edit_item.php?id=<?php se($item, "id"); ?>">Edit</a>
                            </td>
                            <?php endif; ?>
                        <!-- example form submit-->
                        <form action="product_details.php" method="PUT">
                            <input type="hidden" name="id" value="<?php se($item, 'id'); ?>" />
                            <button onclick="details('<?php se($item, 'id'); ?>','<?php se($item, 'cost'); ?>')" class="btn btn-primary">product details</Details></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- this will be moved into a partial file for reusability-->
    <?php include(__DIR__ . "/../../partials/pagination.php"); ?>
</div>
<?php
require(__DIR__ . "/../../partials/footer.php");
?>
