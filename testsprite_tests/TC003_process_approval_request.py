import requests

BASE_URL = "http://localhost/IT-Inventory-Management-System/"
TIMEOUT = 30
HEADERS = {"Content-Type": "application/json"}

def test_process_approval_request():
    # Step 1: Submit an approval request
    approval_request_payload = {
        "action_type": "ADD",
        "requested_by": "test_user",
        "factory": "AGL",
        "asset_name": "Test Asset for Approval"
    }
    approval_request_url = BASE_URL + "request_approval_agl.php"

    resp_req = requests.post(approval_request_url, json=approval_request_payload, headers=HEADERS, timeout=TIMEOUT)
    assert resp_req.status_code == 200, f"Submit approval request failed with status {resp_req.status_code}"

    # Cannot continue to process approval since approval_id is not returned as per PRD


test_process_approval_request()
