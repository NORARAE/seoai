# Scan-Scoped Discovery QA

## Authenticated Filament Spot-Check

Use a real authenticated admin/operator session in Filament and verify the following:

1. Open URL Inventory and confirm the `CurrentScanDiscoverySummary` widget renders above the table.
2. Open SEO Opportunities and confirm the `CurrentScanDiscoverySummary` widget renders above the table.
3. Click `New URLs this scan` from the widget and verify:
   - the destination is URL Inventory
   - the table is scoped to the expected active site
   - the `current_scan` filter is active
   - only rows from the pinned scan run are shown
4. Click `New opportunities this scan` from the widget and verify:
   - the destination is SEO Opportunities
   - the table is scoped to the expected active site
   - the `current_scan` filter is active
   - only rows from the pinned scan run are shown
5. In URL Inventory, verify `Scan Status` badges read correctly in context:
   - `New` for URLs first seen in the pinned scan
   - `Existing` for URLs from earlier runs on the same site
6. In SEO Opportunities, verify `Scan Status` badges read correctly in context:
   - `New this scan` for opportunities created by the pinned scan
   - `Existing` for opportunities from earlier runs on the same site
7. Verify no-scan behavior:
   - widget descriptions read clearly when no running, pending, or completed scan exists
   - `current_scan` filter indicator reads clearly when no scan is available
8. Verify active-site switching behavior:
   - switch the active site in the existing workflow
   - reload URL Inventory and SEO Opportunities
   - confirm the widget counts, filter indicator copy, and scan-aware badges reflect the new active site

## Developer Testing Note

The focused feature test suite bypasses Filament's auth middleware only inside the test file.

Reason:

- The tests target scan-scoped resolver, widget URL state, and table filter behavior.
- Filament panel login middleware enforces panel-auth access separately from the resource/query behavior under test.
- Bypassing that middleware in the focused suite isolates scan-scoped correctness without changing application runtime behavior.

Still intentionally covered by manual browser QA:

- authenticated panel navigation and login flow
- visual rendering of widget placement and badge wording
- real interactive active-site switching behavior in Filament
- readability of empty and no-scan messaging in context