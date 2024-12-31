<?php

// Check if a session is already in progress and if not, start one.
if (!session_id()) {
    session_start();
}

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shopping Cart Catalog - Education Project Only</title>

    <link rel="stylesheet" href="css/minismall.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <h2>Product Catalog - Education Project Only</h2>
    <?php

    // Remove following two lines for production
    ini_set('display_errors', 1);
    error_reporting(-1);

    require 'sanitize.php';
    require 'callQuery.php';
    require 'dbConnect.php';

    // If a session variable named 'numItems' has never been set,
    // initialize it to 0
    if (!isset($_SESSION['numItems'])) {
        $_SESSION['numItems'] = 0;
    }
    
    
    ?> 
    <p>Your shopping cart contains <?= $_SESSION['numItems'] ?> item(s)</p>

    <a href="cart_tdetlaff.php"><i class="fa-solid fa-cart-shopping"></i></a> |
    <a href="index.html">Back to product categories</a>
    <?php

    // Set up and run query to get product categories and initialize variables
    $categoryResult = callQuery($pdo, "SELECT catid FROM categories", "Error fetching category info ");

    $catIDs = [];
    $ctr = 0;

    // Step through the result set and store each category id into
    // our $catIDs array.
    while ($category = $categoryResult->fetch()) {

        // arrayName[index] = value;
        $catIDs[$ctr] = $category['catid'];
        $ctr+=1; // auto incrementing with ++ is removed from swift - its slower than +=

    } // end while next row in $categoryResult result set

    // print_r($catIDs)

    //
    // Check if the incoming category the user chose is valid
    //
    $incomingCategoryId = sanitizeString(INPUT_GET, 'cat');


    // if the incoming category is NOT a valid category, then set 
    // incoming category to 1
    if (!isset($incomingCategoryId) || !in_array($incomingCategoryId, $catIDs)) {
        $incomingCategoryId = 1;
    }


    // echo "<h3>category id = $incomingCategoryId</h3>";

    // Save our incoming category id in our session (session variable)
    $_SESSION['cat'] = $incomingCategoryId;

    //
    // Query for all products in the selected category and 
    // display them in a table - one row per product.
    //
    // We will also display some form elements (tags) as well
    //
    $query = "SELECT * FROM products
              WHERE category = $incomingCategoryId";
    
    $errorMsg = "Error fetching product info ";

    $itemsResults = callQuery($pdo, $query, $errorMsg);

    // Start our category's product table by generating the 
    // first (header) row.
    ?> 
    <br><br>
    <form action="cart_tdetlaff.php" method="post">
        <table>
            <tr class="header">
                <th>Image</th>
                <th>Description</th>
                <th>Price - USD</th>
                <th style="background-color: #fff">&nbsp;</th>
            </tr>

    <?php
    //
    // Step through the result set of products for this category
    // and display each product and its related info in its own table row
    //
    while ($productRow = $itemsResults->fetch()) {

        // print_r($productRow);

        // Convert any special HTML characters to their corresponding
        // HTML entity codes.   Example: & --> &amp;
        //
        // Also, strip off any HTML tags found in the data
        // 
        // Note: could also use sanitization functions to strip out tags
        //
        $imagePath = htmlspecialchars(strip_tags($productRow['loc']));
        $description = htmlspecialchars(strip_tags($productRow['description']));
        $price = htmlspecialchars(strip_tags($productRow['price']));


        //$price = "$" . number_format($price, 2);
        // Now let's do the above an alternate way
        $price = sprintf('$%.2f', $price);


        $productID = $productRow['prodid'];


        // Set $qty to contain what is in our session cart array variable.
        // If your session cart array element of $_SESSION for this product
        // is empty, set the $qty variable to its default value of 0.
        //
        // If the cart element for this product is NOT empty (implying that 
        // a value already exists in the cart for this product), then grab
        // its quantity (value) for display on this page.
        //
        // Note that $_SESSION['cart'] is an array.
        //
        // Remember that each element of an array is like a variable that 
        // holds a value.  That value can be another array so an array
        // element can hold another array as is the case here with 
        // $_SESSION['cart'].
        //
        // We can now deduce that $_SESSION['cart'] is an associative array
        // whose keys are the product ID's and whose values are the quantity
        // for the product that user wishes to purchase.

        //$_SESSION['cart'][2] = 7;
        //$_SESSION['cart'][10] = 4;

        if (isset($_SESSION['cart'][$productID])) {
            $qty = $_SESSION['cart'][$productID];
        } else { // this element of the cart array is empty
            $qty = 0;
        }



        //
        // Build and display a table row for this product using a Here Document
        // named TABLEROW
        //
        ?> 
            <tr>
                <td><img src="<?= $imagePath ?>" alt="Image of <?= $description ?>"></td>
                <td class="desc"><?= $description ?></td>
                <td class="price"><?= $price ?></td>
                <td class="qty">
                    <label for="quanitityForProduct<?= $productID ?>">Qty</label>
                    <input type="text" name="<?= $productID ?>" id="quanitityForProduct<?= $productID ?>" value="<?= $qty ?>" size="1em">
                </td>
            </tr>

        <?php

        }   // end while next product row
    ?> 
            <tr>
                <td colspan="4" id="submitCell">
                    <input type="submit" name="addCart" value="Add Items to Cart">
                </td>
            </tr>
        </table>
    </form>
    <br>
    <a href="cart_tdetlaff.php"><i class="fa-solid fa-cart-shopping"></i></a> |
    <a href="index.html">Back to product categories</a>
    
</body>
</html>