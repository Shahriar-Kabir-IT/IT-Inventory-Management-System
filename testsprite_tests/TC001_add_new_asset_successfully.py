import requests
import uuid

BASE_URL = "http://localhost/IT-Inventory-Management-System"

def test_add_new_asset_successfully():
    url = f"{BASE_URL}/add_asset.php"
    headers = {
        "Content-Type": "application/json"
    }
    # Generate unique asset_name to avoid duplicates
    unique_id = str(uuid.uuid4())
    payload = {
        "asset_name": f"TestAsset_{unique_id}",
        "location": "Head Office",
        "category": "Laptop"
    }
    timeout = 30

    try:
        response = requests.post(url, json=payload, headers=headers, timeout=timeout)
        assert response.status_code == 200, f"Expected status code 200 but got {response.status_code}"
        # The response description is 'Asset added successfully', assume body or text to contain confirmation
        assert "success" in response.text.lower() or "added" in response.text.lower(), "Response does not confirm asset addition"
    finally:
        # Cleanup code can be added here if deletion endpoint is available
        pass

test_add_new_asset_successfully()
