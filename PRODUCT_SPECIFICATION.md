# Product Specification Document: Elmuna Zakat V2

## 1. Product Overview

**Elmuna Zakat V2** is a comprehensive, modern web application designed to manage the collection, inventory, and administration of Zakat (alms), Fidyah, and other forms of Islamic charitable donations. The system acts as a digital ledger and operational tool for Zakat collection centers (Amil), facilitating transactions, managing rice inventory, tracking shift handovers among officers, and providing digital receipts to donors.

The application features a responsive, mobile-first, and highly interactive user interface powered by React and Three.js, backed by a robust Laravel API.

## 2. Core Features & Modules

### 2.1. Transaction Management
The core of the system is a unified transaction engine that allows Amil (officers) to process various types of donations and purchases within a single checkout flow.

*   **Zakat Fitrah (Rice Collection):** Records the collection of Zakat Fitrah paid in the form of rice (measured in kilograms).
*   **Zakat Maal (Wealth Zakat):** Processes monetary payments for Zakat on wealth.
*   **Fidyah:** Manages Fidyah payments, which can be accepted either as monetary amounts or as physical quantities of food (rice).
*   **Shodaqoh & Infaq (General Donations):** Tracks voluntary charitable contributions, accepted in cash or as physical quantities.
*   **Rice Sales (Penjualan Beras):** Allows the center to sell rice directly to individuals who wish to purchase it on-site to fulfill their Zakat Fitrah obligations.

**Transaction Details:**
*   Each transaction generates a unique, sequential transaction number.
*   Captures donor/customer details: Name, Address, and WhatsApp Number.
*   A single transaction can contain multiple items (e.g., a donor paying Zakat Fitrah for 3 people, Fidyah for 1 person, and buying rice, all in one receipt).
*   Records the specific "giver's name" (Muzakki) for each individual transaction detail, distinct from the primary customer.

### 2.2. Shift Management & Handovers (Buku Kasir)
To ensure accountability, the system implements a shift handover mechanism for officers operating the collection desks.

*   **Shift Tracking:** Officers operate within defined shifts (e.g., Shift 1, Shift 2).
*   **End-of-Shift Reconciliation:** When a shift ends, the system automatically calculates the total collections during that period:
    *   **Total Cash (Rp):** Segregated by Rice Sales, Zakat Maal, Fidyah, and Shodaqoh.
    *   **Total Inventory (Kg):** Segregated by Zakat Fitrah (Rice), Fidyah (Rice), and Shodaqoh (Rice).
*   **Handover Record:** Captures the name of the handing-over officer and the receiving officer, creating an immutable audit trail of cash and inventory transfers.

### 2.3. Communication & Receipts
*   **WhatsApp Integration:** Features automated WhatsApp notifications (via background jobs) to send digital receipts and thank-you messages directly to the donor's provided WhatsApp number upon transaction completion.
*   **PDF Receipts:** Utilizes `dompdf` to generate printable PDF receipts for physical distribution if required.

### 2.4. Inventory & Procurement Management
*   **Rice Inventory Management:** Tracks the stock of rice available for sale or distribution.
*   **Purchasing (Pembelian Beras):** Modules to manage the procurement of rice (`PurchaseRice`) from suppliers.
*   **Allocation (`PurchaseRiceAllocation`):** Manages how purchased bulk rice is allocated into standard distribution or sale packages.

### 2.5. Configuration & System Settings
*   **Default Values:** A dedicated module (`default_values`) to manage dynamic system parameters, such as the standard required weight of rice for one person's Zakat Fitrah (e.g., 2.5kg or 2.7kg) or beneficiary allocations.

## 3. User Roles & Access

The application utilizes standard Laravel authentication (via Fortify). While specific ACL (Access Control List) details may vary, the general roles imply:
*   **Amil / Frontdesk Officer:** Can create transactions, manage their shift, and process handovers.
*   **Administrator / Manager:** Can manage inventory, configure default values, view global reports, and oversee all shift handovers.

## 4. Technical Architecture

The application follows a modern monolithic architecture using the Inertia.js paradigm.

### 4.1. Backend (API & Business Logic)
*   **Framework:** Laravel 12.x (PHP 8.2+)
*   **Architecture Pattern:** Service-Repository Pattern. Business logic is strictly decoupled from controllers into dedicated `Services` (e.g., `TransactionService`, `WealthService`), while database interactions are handled by `Repositories`.
*   **Database:** Relational Database (MySQL / SQLite). Migrations map out a highly normalized structure.
*   **Background Processing:** Laravel Queues are used for asynchronous tasks like sending WhatsApp messages (`SendWhatsAppNotification` job) to prevent blocking the main thread during checkout.

### 4.2. Frontend (User Interface)
*   **Framework:** React 18+ with TypeScript.
*   **Bridge:** Inertia.js (allows building single-page applications without building a separate routing API).
*   **Styling:** Modern, responsive design using Vanilla CSS / Tailwind CSS. Features a soft, modern color palette with dark mode support.
*   **3D & Animations:** Utilizes `three`, `@react-three/fiber`, and `framer-motion` for a premium user experience on the landing page (interactive 3D blobs, parallax sections, micro-animations).
*   **Performance:** Mobile-first approach with heavy use of lazy loading for 3D components and dynamic mobile detection to reduce resource usage on lower-end devices.

## 5. Non-Functional Requirements
*   **Soft Deletes:** Critical financial and transaction records (like `transactions` and `transaction_details`) implement soft deletes to prevent accidental data loss and maintain audit integrity.
*   **Performance:** The UI is optimized for fast load times; the heavy 3D landing page elements are lazy-loaded and conditionally simplified for mobile devices.
*   **Scalability:** The backend service-repository pattern and job queues allow the application to scale smoothly during high-traffic periods (like the last days of Ramadan).
