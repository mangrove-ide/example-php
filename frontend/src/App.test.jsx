import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { afterEach, beforeEach, expect, it, vi } from "vitest";
import App from "./App";

const ADA = { id: 1, firstName: "Ada", lastName: "Lovelace", email: "ada@example.com" };
const GRACE = { id: 2, firstName: "Grace", lastName: "Hopper", email: "grace@example.com" };

const jsonResponse = (body, status = 200) => new Response(body == null ? null : JSON.stringify(body), { status });

beforeEach(() => {
    vi.stubGlobal("fetch", vi.fn());
    vi.spyOn(window, "confirm").mockReturnValue(true);
});

afterEach(() => {
    vi.unstubAllGlobals();
    vi.restoreAllMocks();
});

it("lists, adds and removes customers", async () => {
    fetch.mockResolvedValueOnce(jsonResponse([ADA]));
    render(<App />);
    expect(await screen.findByText("Lovelace")).toBeInTheDocument();

    fetch.mockResolvedValueOnce(jsonResponse(GRACE, 201));
    const user = userEvent.setup();
    await user.type(screen.getByLabelText("First name"), "Grace");
    await user.type(screen.getByLabelText("Last name"), "Hopper");
    await user.type(screen.getByLabelText("Email"), "grace@example.com");
    await user.click(screen.getByRole("button", { name: "Add customer" }));
    expect(await screen.findByText("Hopper")).toBeInTheDocument();

    fetch.mockResolvedValueOnce(jsonResponse(null, 204));
    await user.click(screen.getByRole("button", { name: "Remove Ada Lovelace" }));
    expect(screen.queryByText("Lovelace")).not.toBeInTheDocument();
    expect(fetch).toHaveBeenLastCalledWith("/api/customers/1", { method: "DELETE" });
});

it("shows the API error when adding fails", async () => {
    fetch.mockResolvedValueOnce(jsonResponse([]));
    render(<App />);
    await screen.findByText(/No customers yet/);

    fetch.mockResolvedValueOnce(jsonResponse({ error: "A customer with this email already exists" }, 409));
    const user = userEvent.setup();
    await user.type(screen.getByLabelText("First name"), "Ada");
    await user.type(screen.getByLabelText("Last name"), "Lovelace");
    await user.type(screen.getByLabelText("Email"), "ada@example.com");
    await user.click(screen.getByRole("button", { name: "Add customer" }));
    expect(await screen.findByRole("alert")).toHaveTextContent("already exists");
});
