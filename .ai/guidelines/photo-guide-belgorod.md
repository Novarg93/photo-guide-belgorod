# Photo Guide Belgorod – Product Rules (MVP)

This is a local photo session aggregator for Belgorod.

Current phase: MVP validation.

## IN SCOPE
- Categories (wedding, family)
- Locations
- Studios
- Photographers
- Articles
- Brief generator with public share link

## OUT OF SCOPE
- user accounts
- roles
- ratings
- payments
- AI features
- self-service dashboards
- marketplace features

## Engineering Rules
- Keep architecture simple
- Do not introduce new packages unless required
- Follow Laravel + Filament conventions
- Prefer Eloquent relationships
- Use eager loading on listing pages
- Keep controllers thin

## Brief Requirements
- UUID public_token
- Public accessible link
- Copy link button
- No authentication required