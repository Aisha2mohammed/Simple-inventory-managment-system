<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
        }
        form, table {
            width: 100%;
            margin: 20px 0;
        }
        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Search Items</h1>
        <form method="GET">
            <input type="text" name="search_term" placeholder="Enter Item ID or Material">
            <select name="category">
                <option value="" disabled selected>Select Category</option>
                <option value="renewable">Renewable</option>
                <option value="non-renewable">Non-Renewable</option>
            </select>
            <select name="subcategory">
                <option value="" disabled selected>Select Subcategory</option>
                <option value="Furniture">Furniture</option>
                <option value="Electronics">Electronics</option>
                <option value="Metals">Metals</option>
                <option value="Chemicals">Chemicals</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <?php
        require_once 'includes/db_connect.php';
        require_once 'InventoryActions.php';

        $inventoryActions = new InventoryActions($conn);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $search_term = $_GET['search_term'] ?? null;
            $category = $_GET['category'] ?? null;
            $subcategory = $_GET['subcategory'] ?? null;

            $items = $inventoryActions->searchItems($search_term, $category, $subcategory);

            if (!empty($items)) {
                echo "<table><thead><tr><th>Item ID</th><th>Category</th><th>Subcategory</th><th>Material</th><th>Condition</th><th>Quantity</th><th>Status</th></tr></thead><tbody>";
                foreach ($items as $item) {
                    echo "<tr>
                        <td>{$item['item_id']}</td>
                        <td>{$item['category']}</td>
                        <td>{$item['subcategory']}</td>
                        <td>{$item['material']}</td>
                        <td>{$item['condition']}</td>
                        <td>{$item['quantity']}</td>
                        <td>{$item['status']}</td>
                    </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No items found.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
