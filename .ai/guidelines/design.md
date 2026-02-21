# UI Rules

## Base Layout (mandatory)
All public pages must use the existing default app layout:
- AppShell
- AppHeader
- AppContent
Do not create new page-level wrappers unless explicitly requested.

## Footer
Use a shared component: resources/js/components/AppFooter.vue.
Footer is included in the default layout.

## UI Components
Prefer shadcn-vue UI primitives when available:
- Button, Card, Badge, Input, Select, Dialog, Separator
Import from @/components/ui/*.

Do not create custom Tailwind button/card primitives.
Tailwind is allowed for layout/spacing/typography only.

## Text
Avoid generating Russian text in code. Use English placeholders. Russian copy will be added manually later.