import requests

BASE_URL = "http://localhost/IT-Inventory-Management-System"
TIMEOUT = 30
HEADERS = {"Content-Type": "application/json"}

def test_submit_approval_request_agl():
    url = f"{BASE_URL}/request_approval_agl.php"
    test_payloads = [
        {
            "action_type": "ADD",
            "requested_by": "test_user_add",
            "factory": "AGL",
            "asset_name": "TestAsset_Add_001"
        },
        {
            "action_type": "SERVICE",
            "requested_by": "test_user_service",
            "factory": "AGL",
            "asset_name": "TestAsset_Service_001"
        },
        {
            "action_type": "DELETE",
            "requested_by": "test_user_delete",
            "factory": "AGL",
            "asset_name": "TestAsset_Delete_001"
        }
    ]

    for payload in test_payloads:
        try:
            response = requests.post(url, json=payload, headers=HEADERS, timeout=TIMEOUT)
            assert response.status_code == 200, f"Expected status 200 but got {response.status_code}"
            # Response content could be JSON or plain text confirming request submission
            # Validate no error keywords in response text
            resp_text = response.text.lower()
            assert "error" not in resp_text and "exception" not in resp_text and "warning" not in resp_text, \
                "Response contains error indication"
        except requests.RequestException as e:
            assert False, f"Request failed: {e}"

test_submit_approval_request_agl()