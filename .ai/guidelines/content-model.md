# Content model rules (MVP+)

## Entities
- Category
- Example (preset / scenario)
- Photo (single image)

## Hard rules
1) Example belongs to exactly one Category (`examples.category_id`).
2) Photo belongs to exactly one Category (`photos.category_id`).
3) Photo can be attached to Example only if they share the same category:
   `photos.category_id === examples.category_id`.

## Filtering
- Catalog filtering ALWAYS happens on Photo fields (mood/season/location/clothing/etc).
- Example is a preset that can:
  - set default filters for UI
  - define a curated set of photos via pivot `example_photo`
- A photo must still have its own tags (no inheritance from Example).

## Upload workflow (future)
- Photographers submit photos into an Example (bulk upload).
- On upload, photo tags are prefilled from Example tags, then editable per photo.
- Photos go to moderation queue; only approved photos are public.