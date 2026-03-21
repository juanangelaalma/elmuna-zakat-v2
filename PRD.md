# Product Requirements Document (PRD): Elmuna Zakat v2

## 1. Executive Summary

- **Problem Statement**: Zakat management in mosque environments is often manual, decentralized, and lacks real-time transparency, leading to operational inefficiencies for Amils and lower trust levels among Muzakkis.
- **Proposed Solution**: A centralized, digital zakat management system that mimics professional accounting processes (sales/purchases) but is tailored for zakat business logic. It features automated donor receipts via WhatsApp, shift-based accountability, and a real-time public transparency dashboard.
- **Success Criteria**:
  - 100% elimination of paper receipts for new transactions.
  - Real-time visibility of total funds (money and rice) for Mosque management.
  - Public trust increase evidenced by >20% growth in Muzakki participation.
  - Verification of 100% shift handovers with zero balance discrepancies.

## 2. User Experience & Functionality

### User Personas
1. **Muzakki (Donors)**: Individuals paying zakat/donations who want instant proof of payment and to see how their funds contribute to the community.
2. **Amil (Collectors)**: Staff/Volunteers who need a fast way to record transactions and handover duties without manual tallying errors.
3. **Admin / Mosque Takmir**: Oversight body needing audit logs, comprehensive analytics, and fund management tools.

### User Stories & Acceptance Criteria
| User Story | Acceptance Criteria |
| :--- | :--- |
| **As an Amil**, I want to record a zakat payment (rice/money) so that I don't have to write a manual receipt. | • Transaction is logged with a unique ID.<br>• Data reflects in the dashboard immediately.<br>• System prevents saving without required fields (name, phone, amount). |
| **As a Muzakki**, I want to receive a WhatsApp receipt instantly so that I have a digital record of my payment. | • WhatsApp message is triggered within 5s of transaction save.<br>• Message contains transaction details and a link to the receipt. |
| **As a Takmir**, I want to see a live "Public Dashboard" so that the community can see the real-time progress of zakat collection. | • Public URL (`/zakat-live`) is accessible without login.<br>• Data refreshes automatically every 30 seconds.<br>• Visualizes totals for Rice, Money, and Muzakki count. |
| **As an Amil**, I want to perform a 'Shift Handover' so that the next team knows exactly how much physical cash/rice they are taking over. | • Calculates total expected balances for the shift.<br>• Requires Amil signature/confirmation.<br>• Logs discrepancies for Admin review. |

### Non-Goals (Current Phase)
- **Mustahiq Management**: Automated verification of recipients is planned for future sessions.
- **Advanced Accounting**: Double-entry bookkeeping and balance sheets are out of scope for the current MVP.
- **Payment Gateway Integration**: Online payments (QRIS/Bank Transfer) are not included; current focus is manual recording of cash/physical rice.

## 3. Technical Specifications

### Architecture Overview
- **Frontend**: React 18+ with Inertia.js for a SPA-like experience within the Laravel framework.
- **Backend**: Laravel 11.x (PHP 8.2+).
- **Styling**: Vanilla CSS and Tailwind CSS (specifically for the Live Dashboard).
- **UI Library**: Shadcn/UI (Radix UI + Tailwind) for the administrative dashboard.
- **State Management**: Managed via Inertia props and local React state.

### Integration Points
- **WhatsApp API**: Chatery Integration for automated messaging.
- **Database**: Relational DB (MySQL/PostgreSQL) for transaction integrity and soft deletes.
- **Auth**: Laravel Fortify for secure session-based authentication.

### Security & Privacy
- **Data Protection**: Muzakki phone numbers must be encrypted or strictly masked in public views.
- **Access Control**: Role-based access for Amil vs. Admin vs. Public.
- **Auditability**: Soft deletes enabled for transactions to prevent data loss and allow for recovery/audit.

## 4. Risks & Roadmap

### Phased Rollout
1. **MVP (Current)**: Digital recording, WhatsApp receipts, Shift Handover, Live Dashboard.
2. **v1.1 (Planned)**: Basic Mustahiq logging (manual entry of distributions).
3. **v2.0 (Vision)**: Advanced accounting reports, automated Mustahiq verification, and QRIS integration.

### Technical Risks
- **WhatsApp Delivery Failures**: Dependence on third-party API (Chatery); mitigated by logging delivery status (`is_wa_sent`) in the database.
- **Internet Connectivity**: Mosque environments may have unstable internet; system requires a reliable connection for real-time dashboard updates.
