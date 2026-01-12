import requests

def test_retrieve_all_assets_head_office():
    base_url = "http://localhost/IT-Inventory-Management-System"
    endpoint = "/get_assets.php"
    url = base_url + endpoint
    timeout = 30
    headers = {
        "Accept": "application/json"
    }
    try:
        response = requests.get(url, headers=headers, timeout=timeout)
        response.raise_for_status()
    except requests.exceptions.RequestException as e:
        assert False, f"Request failed: {e}"

    # Validate status code
    assert response.status_code == 200, f"Expected status code 200, got {response.status_code}"

    # Validate response content type
    content_type = response.headers.get("Content-Type", "")
    assert "application/json" in content_type, f"Expected 'application/json' content type, got {content_type}"

    # Validate response body structure
    try:
        assets = response.json()
    except ValueError as e:
        assert False, f"Response is not valid JSON: {e}"

    # Validate it is a list (array)
    assert isinstance(assets, list), f"Expected list of assets, got {type(assets)}"

    # Each asset should be a dict/object
    for asset in assets:
        assert isinstance(asset, dict), f"Expected asset to be a dict, got {type(asset)}"

    # Additional validation can be done here on asset fields if schema known, 
    # but as per PRD, only checking format and status code
    
test_retrieve_all_assets_head_office()