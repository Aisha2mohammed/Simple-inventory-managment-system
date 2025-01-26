<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'includes/db_connect.php'; 
require_once 'Inventory.php';
require_once 'Security.php'; 

session_start(); 
$security = new Security($conn); 

$security->enforceSessionTimeout(); 
$security->checkAuthentication(); 
$security->checkAuthorization('department_head');


$inventory = new Inventory($conn);
$message = "";
$itemExistsMessage = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $itemId = $_POST['item_id'];
        
        // Check if the item ID already exists in the inventory
        $existingItem = $inventory->getItemById($itemId);
        if ($existingItem) {
            $itemExistsMessage = "Item with ID $itemId already exists.";
        } else {
            $data = [
                'item_id' => $_POST['item_id'],
                'category' => $_POST['category'],
                'subcategory' => $_POST['subcategory'],
                'material' => $_POST['material'],
                'condition' => $_POST['condition'],
                'quantity' => $_POST['quantity'],
                'status' => $_POST['status'],
            ];
            if ($inventory->addItem($data)) {
                $message = "Item added successfully!";
            } else {
                $message = "Failed to add item.";
            }
        }
    } elseif ($action === 'delete') {
        if ($inventory->deleteItem($_POST['item_id'])) {
            $message = "Item deleted successfully!";
        } else {
            $message = "Failed to delete item.";
        }
    }
}

// Fetch all items
$items = $inventory->getAllItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items</title>
    <style>
        /* Existing styling */
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
        h1, h2 {
            text-align: center;
            color: #333;
        }
        form, table {
            width: 100%;
            margin: 20px 0;
        }
        form input, form select, form button ,.form-button{
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
        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .error-message {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Items</h1>

        <!-- Notification for item existence -->
        <?php if ($itemExistsMessage): ?>
            <div class="error-message"><?= $itemExistsMessage ?></div>
        <?php endif; ?>

        <!-- Add Item Form -->
        <h2>Add/Update Item</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">

            <label for="item_id">Item ID:</label>
            <input type="text" id="item_id" name="item_id" placeholder="Enter Item ID" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="" disabled selected>Select Category</option>
                <option value="renewable">Renewable</option>
                <option value="non-renewable">Non-Renewable</option>
            </select>

            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory" required>
                <!-- Dynamic options -->
            </select>

            <label for="material">Material:</label>
            <select id="material" name="material" required>
                <!-- Dynamic options -->
            </select>

            <label for="condition">Condition:</label>
            <select name="condition" required>
                <option value="new">New</option>
                <option value="old">Old</option>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>

            <label for="status">Status:</label>
            <select name="status" required>
                <option value="available">Available</option>
                <option value="borrowed">Borrowed</option>
            </select>

            <button type="submit">Add/Update Item</button>
        </form>

        <h2>Existing Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Material</th>
                    <th>Condition</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_id']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['subcategory']) ?></td>
                        <td><?= htmlspecialchars($item['material']) ?></td>
                        <td><?= htmlspecialchars($item['condition']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                        <td>
                            <a href="update_item.php?item_id=<?= htmlspecialchars($item['item_id']) ?>" style="text-decoration: none;">
                                <button type="button" class="form-button" style="background-color: #0000ff7a; color: white;">Update</button>
                            </a>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                <button type="submit" style="background-color: #ff00009c; color: white;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Existing JavaScript for dynamic options
        const subcategoryMap = {
            "renewable": ["Furniture", "Electronics", "Solar Panels", "Wind Turbines"],
            "non-renewable": ["Metals", "Chemicals", "Plastics", "Fossil Fuels"]
        };

        const materialMap = {
            "Furniture": ["Chair", "Table", "Cupboard", "Sofa", "Bookshelf"],
            "Electronics": ["Desktop", "Laptop", "Monitor", "Keyboard", "Mouse"],
            "Solar Panels": ["Monocrystalline", "Polycrystalline", "Thin Film"],
            "Wind Turbines": ["Small Turbine", "Medium Turbine", "Large Turbine"],
            "Metals": ["Iron", "Copper", "Aluminum", "Steel", "Brass"],
            "Chemicals": ["Acid", "Base", "Solvent", "Oxidizer", "Salt"],
            "Plastics": ["Bottle", "Container", "Pipe", "Sheet", "Bag"],
            "Fossil Fuels": ["Coal", "Oil", "Natural Gas", "LPG"]
        };

        const categoryDropdown = document.getElementById("category");
        const subcategoryDropdown = document.getElementById("subcategory");
        const materialDropdown = document.getElementById("material");

        categoryDropdown.addEventListener("change", function () {
            const selectedCategory = this.value;
            subcategoryDropdown.innerHTML = `<option value="" disabled selected>Select Subcategory</option>`;
            (subcategoryMap[selectedCategory] || []).forEach(sub => {
                subcategoryDropdown.innerHTML += `<option value="${sub}">${sub}</option>`;
            });
        });

        subcategoryDropdown.addEventListener("change", function () {
            const selectedSubcategory = this.value;
            materialDropdown.innerHTML = `<option value="" disabled selected>Select Material</option>`;
            (materialMap[selectedSubcategory] || []).forEach(mat => {
                materialDropdown.innerHTML += `<option value="${mat}">${mat}</option>`;
            });
        });
    </script>
</body>
</html>
