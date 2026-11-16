const CUSTOMERS_URL = "/api/customers";

async function request(url, options) {
    const response = await fetch(url, options);
    if (!response.ok) {
        const body = await response.json().catch(() => ({}));
        throw new Error(body.error ?? `Request failed with status ${response.status}`);
    }
    return response.status === 204 ? null : response.json();
}

export const listCustomers = () => request(CUSTOMERS_URL);

export const createCustomer = customer => request(CUSTOMERS_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(customer),
});

export const deleteCustomer = customerId => request(`${CUSTOMERS_URL}/${customerId}`, { method: "DELETE" });
