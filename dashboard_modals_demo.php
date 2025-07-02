<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Asset Modal Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 40px;
        }

        .ref-modal {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 900px;
            width: 95%;
            margin: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin-bottom: 40px;
        }

        .ref-modal h2 {
            font-size: 24px;
            color: #343a40;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .ref-modal label {
            font-weight: bold;
            font-size: 14px;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .ref-modal select,
        .ref-modal input,
        .ref-modal textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            margin-bottom: 20px;
            background-color: white;
        }

        .ref-modal textarea {
            resize: vertical;
            height: 80px;
        }

        .ref-modal .warning-box {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-left: 4px solid #dc3545;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .ref-modal .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .ref-modal .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .ref-modal .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .ref-modal .btn-green {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .ref-modal .btn-orange {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }

        .ref-modal .btn-red {
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
            color: white;
        }

        .ref-modal .btn-grey {
            background: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<!-- Add New Item Modal -->
<div class="ref-modal">
    <h2>➕ Add New Asset</h2>
    <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div><label>Asset Name*</label><input type="text" required></div>
        <div><label>Category*</label><select required><option>Select Category</option></select></div>
        <div><label>Brand*</label><input type="text" required></div>
        <div><label>Model*</label><input type="text" required></div>
        <div><label>Serial Number*</label><input type="text" required></div>
        <div><label>Location*</label><input type="text" required></div>
        <div><label>Assigned To</label><input type="text"></div>
        <div><label>Department*</label><input type="text" required></div>
        <div><label>Purchase Date*</label><input type="date" required></div>
        <div><label>Purchase Price*</label><input type="text" placeholder="$1,000" required></div>
        <div><label>Warranty Expiry*</label><input type="date" required></div>
        <div><label>Priority</label><select><option>Low</option><option selected>Medium</option><option>High</option></select></div>
        <div style="grid-column: 1 / -1;"><label>Notes</label><textarea placeholder="Additional information..."></textarea></div>
    </form>
    <div class="button-group">
        <button class="btn btn-green">Add Asset</button>
        <button class="btn btn-grey">Cancel</button>
    </div>
</div>

<!-- Send for Servicing Modal -->
<div class="ref-modal">
    <h2>🔧 Send Asset for Servicing</h2>
    <label>Select Asset to Service:</label>
    <select><option>Choose an asset...</option></select>
    <label>Service Type:</label>
    <select><option>Scheduled Maintenance</option></select>
    <label>Service Notes:</label>
    <textarea placeholder="Describe the service required..."></textarea>
    <div class="button-group">
        <button class="btn btn-orange">Send for Service</button>
        <button class="btn btn-grey">Cancel</button>
    </div>
</div>

<!-- Remove Item Modal -->
<div class="ref-modal">
    <h2>🗑️ Remove Asset from Inventory</h2>
    <label>Select Asset to Remove:</label>
    <select><option>Choose an asset...</option></select>
    <label>Reason for Removal:</label>
    <select><option>End of Life</option></select>
    <label>Additional Notes:</label>
    <textarea placeholder="Additional details about removal..."></textarea>
    <div class="warning-box">
        ⚠️ <strong>Warning:</strong> This action will permanently remove the asset from your inventory.
    </div>
    <div class="button-group">
        <button class="btn btn-red">Remove Asset</button>
        <button class="btn btn-grey">Cancel</button>
    </div>
</div>

</body>
</html>
