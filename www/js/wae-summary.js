const wae_summary_loading = document.getElementById('wae-summary-loading');
if (wae_summary_loading === null) {
    throw Error('Missing #wae-summary-loading element');
}

const wae_summary_error = document.getElementById('wae-summary-error');
if (wae_summary_error === null) {
    throw Error('Missing #wae-summary-error element');
}

const wae_summary_no_data = document.getElementById('wae-summary-no-data');
if (wae_summary_no_data === null) {
    throw Error('Missing #wae-summary-no-data element');
}

const wae_summary_yes_data = document.getElementById('wae-summary-yes-data');
if (wae_summary_yes_data === null) {
    throw Error('Missing #wae-summary-yes-data element');
}

const wae_date = document.getElementById('wae-date');
if (wae_date === null) {
    throw Error('Missing #wae-date element');
}
const wae_more_info_link= document.getElementById('wae-more-info-link');
if (wae_more_info_link === null) {
    throw Error('Missing #wae-more-info-link element');
}
const wae_pdf_count= document.getElementById('wae-pdf-count');
if (wae_pdf_count === null) {
    throw Error('Missing #wae-pdf-count element');
}
const wae_doc_count= document.getElementById('wae-doc-count');
if (wae_doc_count === null) {
    throw Error('Missing #wae-doc-count element');
}
const wae_pdf_not_tagged = document.getElementById('wae-pdf-not-tagged');
if (wae_pdf_not_tagged === null) {
    throw Error('Missing #wae-pdf-not-tagged element');
}
const wae_pdf_violations = document.getElementById('wae-pdf-violations');
if (wae_pdf_violations === null) {
    throw Error('Missing #wae-pdf-violations element');
}

// These come from the template file
if (typeof window.wae_api !== 'string') {
    throw Error('Missing WAE api');
}
if (typeof window.wae_base_url !== 'string') {
    throw Error('Missing Site Base URL');
}

/**
 * Gets the WAE document scan data and displays it on the page
 */
async function fetch_data() {

    // Reset Sections
    wae_summary_loading.classList.remove('dcf-d-none');
    wae_summary_error.classList.add('dcf-d-none');
    wae_summary_no_data.classList.add('dcf-d-none');
    wae_summary_yes_data.classList.add('dcf-d-none');


    // Set up the API url
    const wae_api = new URL(window.wae_api);
    wae_api.searchParams.append('url', window.wae_base_url);
    wae_api.searchParams.append('upload_type', 'auto scan');

    try {
        // Get recent scans
        const response_recent_scan = await fetch(wae_api);
        if (!response_recent_scan.ok) {
            wae_summary_loading.classList.add('dcf-d-none');
            wae_summary_error.classList.remove('dcf-d-none');
            throw new Error(`Error fetching wae auto crawl api: ${response_recent_scan.status} (${response_recent_scan.statusText || 'Unknown'})`);
        }
        let json_recent_scan_data = await response_recent_scan.json();
        console.log(json_recent_scan_data);

        // If there are no recent auto scans then try site crawl
        if (!Array.isArray(json_recent_scan_data) || json_recent_scan_data.length === 0) {
            wae_api.searchParams.append('upload_type', 'subdomain');

            const response_site_crawl = await fetch(wae_api);
            if (!response_site_crawl.ok) {
                wae_summary_loading.classList.add('dcf-d-none');
                wae_summary_error.classList.remove('dcf-d-none');
                throw new Error(`Error fetching wae subdomain api: ${response_site_crawl.status} (${response_site_crawl.statusText || 'Unknown'})`);
            }

            json_recent_scan_data = await response_site_crawl.json();

            // If we still don't have anything then there is no data
            if (!Array.isArray(json_recent_scan_data) || json_recent_scan_data.length === 0) {
                wae_summary_loading.classList.add('dcf-d-none');
                wae_summary_no_data.classList.remove('dcf-d-none');
                throw new Error('No Site Data');
            }
        }

        // Get the most recent valid scan
        let first_scan = null;
        for (let i = 0; i < json_recent_scan_data.length; i++) {
            if (json_recent_scan_data[i].status !== 'finished') {
                continue;
            }
            if (json_recent_scan_data[i].is_canceled === true) {
                continue;
            }
            if (json_recent_scan_data[i].upload_type !== 'auto scan' && json_recent_scan_data[i].upload_type !== 'subdomain') {
                continue;
            }
            first_scan = json_recent_scan_data[i];
            break;
        }

        // If we still don't have the first scan then there is no data
        if (first_scan === null) {
            wae_summary_loading.classList.add('dcf-d-none');
            wae_summary_no_data.classList.remove('dcf-d-none');
            throw new Error('No Good Scan Data Found');
        }

        const response_scan = await fetch(first_scan.api_endpoint);
        if (!response_scan.ok) {
            wae_summary_loading.classList.add('dcf-d-none');
            wae_summary_error.classList.remove('dcf-d-none');
            throw new Error(`Error fetching wae scan data: ${response_scan.status} (${response_scan.statusText || 'Unknown'})`);
        }
        const scan_data = await response_scan.json();
        console.log(scan_data);

        wae_summary_loading.classList.add('dcf-d-none');
        wae_summary_yes_data.classList.remove('dcf-d-none');

        // Get/set and format date
        const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        const created_at_date = new Date(Date.parse(scan_data.created_at));
        const formatted_date = `${months[created_at_date.getMonth()]} ${created_at_date.getDate()}, ${created_at_date.getFullYear()}`
        wae_date.innerText = `on ${formatted_date}`;

        // Set the more info href
        wae_more_info_link.setAttribute('href', scan_data.view_scan_endpoint);

        // Set the counts
        wae_pdf_count.innerText = scan_data.pdf_count;
        wae_doc_count.innerText = scan_data.doc_count - scan_data.pdf_count;

        // Set the non-tagged pdf count
        // If more than 0 then it is invalid
        wae_pdf_not_tagged.innerText = scan_data.non_tagged_pdf_count;
        if (scan_data.non_tagged_pdf_count > 0) {
            wae_pdf_not_tagged.parentElement.classList.remove('valid');
            wae_pdf_not_tagged.parentElement.classList.add('invalid');
        } else {
            wae_pdf_not_tagged.parentElement.classList.add('valid');
            wae_pdf_not_tagged.parentElement.classList.remove('invalid');
        }

        // Set the pdf violation count
        // If more than 0 then it is invalid
        wae_pdf_violations.innerText = scan_data.pdf_violation_count;
        if (scan_data.pdf_violation_count > 0) {
            wae_pdf_violations.parentElement.classList.remove('valid');
            wae_pdf_violations.parentElement.classList.add('invalid');
        } else {
            wae_pdf_violations.parentElement.classList.add('valid');
            wae_pdf_violations.parentElement.classList.remove('invalid');
        }
    } catch (err) {
        if (err.message !== 'No Site Data') {
            // If we are here then something bad happened
            wae_summary_loading.classList.add('dcf-d-none');
            wae_summary_error.classList.remove('dcf-d-none');
            console.error(err);
        }
    }
}
window.wae_fetch_data = fetch_data;
fetch_data();

