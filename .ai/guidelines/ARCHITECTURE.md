# Architecture Overview – Photo Guide Belgorod

## Architecture Style

Content-driven Laravel application with:
- Filament admin panel
- Inertia/Vue frontend
- SEO-first public routing

No role-based multi-user system in MVP.

---

## Database Design

### Category
Belongs to many Locations
Belongs to many Studios
Belongs to many Photographers

### Location
Belongs to many Categories

### Studio
Belongs to many Categories

### Photographer
Belongs to many Categories

### Article
Belongs to Category

### Brief
Belongs to Category
Belongs to Location (optional)

---

## Filtering Logic

Category page supports:
- Season filter
- Location type filter
- Budget filter (range-based)

Season logic:
If selected date is between best_from and best_to → prioritize location.

---

## Brief System

Flow:
1. User selects parameters in /brief/new
2. Server validates and creates Brief record
3. UUID generated as public_token
4. Redirect to /brief/{token}

Public Brief Page:
- Display selected parameters
- Display related locations
- Display estimated budget
- CTA copy link

---

## SEO Strategy

- Slugs for all entities
- Dynamic meta tags
- OpenGraph support
- Sitemap
- Structured data (LocalBusiness later)

---

## Scalability Considerations

Future-proofing:
- public_token allows external sharing
- Entities structured for multi-city expansion
- Categories generic (not hardcoded)

Phase 2 can introduce:
- roles
- dashboards
- ratings
- AI modules

## Slug Rules

All public entities must have:
- slug (unique)
- public visibility flag
- is_active boolean