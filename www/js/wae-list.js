const wae_list_loading = document.getElementById('wae-list-loading');
if (wae_list_loading === null) {
    throw Error('Missing #wae-list-loading element');
}

const wae_list_error = document.getElementById('wae-list-error');
if (wae_list_error === null) {
    throw Error('Missing #wae-list-error element');
}

const wae_list_no_data = document.getElementById('wae-list-no-data');
if (wae_list_no_data === null) {
    throw Error('Missing #wae-list-no-data element');
}

const wae_list_yes_data = document.getElementById('wae-list-yes-data');
if (wae_list_yes_data === null) {
    throw Error('Missing #wae-list-yes-data element');
}

const wae_list_table = document.getElementById('wae-list-table');
if (wae_list_table === null) {
    throw Error('Missing #wae-list-table element');
}

// These come from the template file
if (typeof window.wae_api !== 'string') {
    throw Error('Missing WAE api');
}
if (typeof window.wae_base_url !== 'string') {
    throw Error('Missing Site Base URL');
}

/**
 * Gets WAE document data and displays it in the table
 */
async function fetch_data() {

    // Reset Sections
    wae_list_loading.classList.remove('dcf-d-none');
    wae_list_error.classList.add('dcf-d-none');
    wae_list_no_data.classList.add('dcf-d-none');
    wae_list_yes_data.classList.add('dcf-d-none');


    // Set up the API url
    const wae_api = new URL(window.wae_api);
    wae_api.searchParams.append('url', window.wae_base_url);
    wae_api.searchParams.append('upload_type', 'auto scan');

    try {
        // Get recent auto scan
        const response_recent_scan = await fetch(wae_api);
        if (!response_recent_scan.ok) {
            wae_list_loading.classList.add('dcf-d-none');
            wae_list_error.classList.remove('dcf-d-none');
            throw new Error(`Error fetching wae recent api: ${response_recent_scan.status} (${response_recent_scan.statusText || 'Unknown'})`);
        }
        let recent_scan_json_data = await response_recent_scan.json();
        console.log(recent_scan_json_data);

        // If there is no data then try a subdomain scan
        if (!Array.isArray(recent_scan_json_data) || recent_scan_json_data.length === 0) {
            wae_api.searchParams.append('upload_type', 'subdomain');

            // Get recent subdomain scan
            const response_subdomain = await fetch(wae_api);
            if (!response_subdomain.ok) {
                wae_list_loading.classList.add('dcf-d-none');
                wae_list_error.classList.remove('dcf-d-none');
                throw new Error(`Error fetching wae subdomain api: ${response_subdomain.status} (${response_subdomain.statusText || 'Unknown'})`);
            }

            recent_scan_json_data = await response_subdomain.json();

            // If there is still no data then there is no data
            if (!Array.isArray(recent_scan_json_data) || recent_scan_json_data.length === 0) {
                wae_list_loading.classList.add('dcf-d-none');
                wae_list_no_data.classList.remove('dcf-d-none');
                throw new Error('No Site Data');
            }
        }

        // Get the most recent valid scan
        let first_scan = null;
        for (let i = 0; i < recent_scan_json_data.length; i++) {
            if (recent_scan_json_data[i].status !== 'finished') {
                continue;
            }
            if (recent_scan_json_data[i].is_canceled === true) {
                continue;
            }
            if (recent_scan_json_data[i].upload_type !== 'auto scan' && recent_scan_json_data[i].upload_type !== 'subdomain') {
                continue;
            }
            first_scan = recent_scan_json_data[i];
            break;
        }

        // If first scan is null then we have no data
        if (first_scan === null) {
            wae_list_loading.classList.add('dcf-d-none');
            wae_list_no_data.classList.remove('dcf-d-none');
            throw new Error('No Good Scan Data Found');
        }

        // Get the scan's data
        const response_scan = await fetch(first_scan.api_endpoint);
        if (!response_scan.ok) {
            wae_list_loading.classList.add('dcf-d-none');
            wae_list_error.classList.remove('dcf-d-none');
            throw new Error(`Error fetching wae scan data: ${response_scan.status} (${response_scan.statusText || 'Unknown'})`);
        }
        const scan_json_data = await response_scan.json();
        console.log(scan_json_data);

        // Get the document data for the scan
        const response_doc = await fetch(scan_json_data.api_doc_endpoint);
        if (!response_doc.ok) {
            wae_list_loading.classList.add('dcf-d-none');
            wae_list_error.classList.remove('dcf-d-none');
            throw new Error(`Error fetching wae doc data: ${response_doc.status} (${response_doc.statusText || 'Unknown'})`);
        }
        const doc_json_data = await response_doc.json();
        console.log(doc_json_data);

        // Loop through all the document pages
        for (let i = 0; i < doc_json_data.length; i++) {

            // get and format the date time
            const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            const created_at_date = new Date(Date.parse(doc_json_data[i].created_at));
            let hours = created_at_date.getHours();
            let ampm = 'am';
            if (hours > 12) {
                hours = hours - 12;
                ampm = 'pm';
            }
            const formatted_date = `${months[created_at_date.getMonth()]} ${created_at_date.getDate()}, ${created_at_date.getFullYear()} ${hours}:${created_at_date.getMinutes()}${ampm}`

            // Set up the table row
            const tr_element = document.createElement('tr');
            tr_element.innerHTML = `
                <td><a href="${doc_json_data[i].source}">${doc_json_data[i].final_url ?? doc_json_data[i].source}</a></td>
                <td>${doc_json_data[i].formatted_content_type}</td>
                <td><a href="${doc_json_data[i].page_found_on_originally}">${doc_json_data[i].page_found_on_originally}</a></td>
                <td>${formatted_date}</td>
                <td><a class="dcf-btn dcf-btn-primary" href="${doc_json_data[i].view_endpoint}">More Info</a></td>
            `;

            // Append the row to the table
            wae_list_table.append(tr_element);
        }

        // Once we are here then all the doc pages loaded
        wae_list_loading.classList.add('dcf-d-none');
        wae_list_yes_data.classList.remove('dcf-d-none');

    } catch (err) {
        // If we get there then something bad happened
        wae_list_loading.classList.add('dcf-d-none');
        wae_list_error.classList.remove('dcf-d-none');
        console.error(err);
    }
}
window.wae_fetch_data = fetch_data;
fetch_data();

