export default function CustomerList({ customers, onDelete }) {
    if (customers.length === 0) {
        return <p className="muted">No customers yet. Add one above.</p>;
    }
    return (
        <table className="customer-table">
            <thead>
                <tr>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th aria-label="Actions" />
                </tr>
            </thead>
            <tbody>
                {customers.map(customer => (
                    <tr key={customer.id}>
                        <td>{customer.firstName}</td>
                        <td>{customer.lastName}</td>
                        <td><a href={`mailto:${customer.email}`}>{customer.email}</a></td>
                        <td className="actions">
                            <button type="button" className="danger" onClick={() => onDelete(customer)}
                                aria-label={`Remove ${customer.firstName} ${customer.lastName}`}>
                                Remove
                            </button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
