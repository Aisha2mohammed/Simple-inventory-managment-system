<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'store_keeper') {
    header("Location: login.html");
    exit;
}

require_once 'includes/db_connect.php';
require_once 'Inventory.php';

$inventory = new Inventory($conn);
$message = "";

// Fetch the item details for pre-filling the form
$item_id = $_GET['item_id'] ?? null;
if (!$item_id) {
    die("Item ID not provided.");
}

$item = $inventory->getItemById($item_id);
if (!$item) {
    die("Item not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'item_id' => $_POST['item_id'],
        'category' => $_POST['category'],
        'subcategory' => $_POST['subcategory'],
        'material' => $_POST['material'],
        'condition' => $_POST['condition'],
        'quantity' => $_POST['quantity'],
    ];

    if ($inventory->updateItem($data)) {
        $message = "Item updated successfully!";
    } else {
        $message = "Failed to update item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        form input, form select, form button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Item</h1>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="renewable" <?= $item['category'] === 'renewable' ? 'selected' : '' ?>>Renewable</option>
                <option value="non-renewable" <?= $item['category'] === 'non-renewable' ? 'selected' : '' ?>>Non-Renewable</option>
            </select>

            <label for="subcategory">Subcategory:</label>
            <select id="subcategory" name="subcategory" required>
                <!-- Options populated dynamically -->
            </select>

            <label for="material">Material:</label>
            <select id="material" name="material" required>
                <!-- Options populated dynamically -->
            </select>

            <label for="condition">Condition:</label>
            <select id="condition" name="condition" required>
                <option value="new" <?= $item['condition'] === 'new' ? 'selected' : '' ?>>New</option>
                <option value="old" <?= $item['condition'] === 'old' ? 'selected' : '' ?>>Old</option>
            </select>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" value="<?= htmlspecialchars($item['quantity']) ?>" required>

            <button type="submit">Update Item</button>
        </form>
    </div>

    <script>
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

        function populateSubcategoryAndMaterial() {
            const selectedCategory = categoryDropdown.value;
            subcategoryDropdown.innerHTML = `<option value="" disabled>Select Subcategory</option>`;
            (subcategoryMap[selectedCategory] || []).forEach(sub => {
                subcategoryDropdown.innerHTML += `<option value="${sub}" ${sub === "<?= htmlspecialchars($item['subcategory']) ?>" ? "selected" : ""}>${sub}</option>`;
            });
            const selectedSubcategory = subcategoryDropdown.value || "<?= htmlspecialchars($item['subcategory']) ?>";
            materialDropdown.innerHTML = `<option value="" disabled>Select Material</option>`;
            (materialMap[selectedSubcategory] || []).forEach(mat => {
                materialDropdown.innerHTML += `<option value="${mat}" ${mat === "<?= htmlspecialchars($item['material']) ?>" ? "selected" : ""}>${mat}</option>`;
            });
        }

        categoryDropdown.addEventListener("change", populateSubcategoryAndMaterial);
        subcategoryDropdown.addEventListener("change", populateSubcategoryAndMaterial);

        // Initial population
        populateSubcategoryAndMaterial();
    </script>
</body>
</html>
