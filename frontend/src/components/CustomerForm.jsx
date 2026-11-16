import { useState } from "react";

const EMPTY_CUSTOMER = { firstName: "", lastName: "", email: "" };

export default function CustomerForm({ onCreate }) {
    const [customer, setCustomer] = useState(EMPTY_CUSTOMER);
    const [isSaving, setIsSaving] = useState(false);
    const [errorMessage, setErrorMessage] = useState(null);

    const updateField = event => {
        const { name, value } = event.target;
        setCustomer(current => ({ ...current, [name]: value }));
    };

    const handleSubmit = async event => {
        event.preventDefault();
        setIsSaving(true);
        try {
            await onCreate(customer);
            setCustomer(EMPTY_CUSTOMER);
            setErrorMessage(null);
        } catch (error) {
            setErrorMessage(error.message);
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <form className="customer-form" onSubmit={handleSubmit}>
            <input name="firstName" placeholder="First name" aria-label="First name" value={customer.firstName}
                onChange={updateField} required />
            <input name="lastName" placeholder="Last name" aria-label="Last name" value={customer.lastName}
                onChange={updateField} required />
            <input name="email" type="email" placeholder="Email" aria-label="Email" value={customer.email}
                onChange={updateField} required />
            <button type="submit" disabled={isSaving}>{isSaving ? "Adding…" : "Add customer"}</button>
            {errorMessage && <p className="error form-error" role="alert">{errorMessage}</p>}
        </form>
    );
}
