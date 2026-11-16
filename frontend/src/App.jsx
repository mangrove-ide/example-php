import { useEffect, useState } from "react";
import { createCustomer, deleteCustomer, listCustomers } from "./api";
import CustomerForm from "./components/CustomerForm";
import CustomerList from "./components/CustomerList";

export default function App() {
    const [customers, setCustomers] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [errorMessage, setErrorMessage] = useState(null);

    useEffect(() => {
        listCustomers()
            .then(setCustomers)
            .catch(error => setErrorMessage(error.message))
            .finally(() => setIsLoading(false));
    }, []);

    const handleCreate = async customer => {
        const created = await createCustomer(customer);
        setCustomers(current => [...current, created].sort((left, right) =>
            left.lastName.localeCompare(right.lastName) || left.firstName.localeCompare(right.firstName)));
    };

    const handleDelete = async customer => {
        if (!window.confirm(`Remove ${customer.firstName} ${customer.lastName}?`)) {
            return;
        }
        try {
            await deleteCustomer(customer.id);
            setCustomers(current => current.filter(existing => existing.id !== customer.id));
            setErrorMessage(null);
        } catch (error) {
            setErrorMessage(error.message);
        }
    };

    return (
        <main className="app">
            <header>
                <h1>Customers</h1>
                <p className="subtitle">{customers.length} total</p>
            </header>
            <CustomerForm onCreate={handleCreate} />
            {errorMessage && <p className="error" role="alert">{errorMessage}</p>}
            {isLoading ? <p className="muted">Loading…</p> : <CustomerList customers={customers} onDelete={handleDelete} />}
        </main>
    );
}
