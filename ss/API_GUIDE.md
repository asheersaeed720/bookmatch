# BookMatch — Mobile App REST API Guide

This document describes every API endpoint built for the BookMatch mobile app:
what it does, when to use it, and the exact request payloads and responses.

It contains **no source code** — only endpoint descriptions and JSON examples.

> **Interactive docs (Scramble):** This project also ships auto-generated, always-up-to-date
> OpenAPI documentation powered by [Scramble](https://scramble.dedoc.co). Once dependencies
> are installed, browse it at **`/docs/api`** (interactive UI with "Try It") and download the
> raw OpenAPI spec at **`/docs/api.json`**. By default the UI is accessible in the local
> environment; to open it in production, define a `viewApiDocs` Gate. This Markdown guide is a
> human-friendly companion to that generated reference.

---

## Basics

- **Base URL:** `https://<your-domain>/api/v1`
- **Format:** All requests and responses use JSON.
- **Required headers (every request):**
  - `Accept: application/json`
  - `Content-Type: application/json` (for requests that send a body)
- **Authentication:** Token-based (Laravel Sanctum).
  After login/register you receive a `token`. Send it on every protected request:
  - `Authorization: Bearer <token>`
- **Auth column (badge):** Each endpoint below is marked **Public** (no token needed) or
  **Protected** (token required).

### Common response notes
- Single records are wrapped in a `data` object.
- Lists are paginated and wrapped in `data` (array) plus `links` and `meta`.
- Validation errors return HTTP `422` with an `errors` object.
- Missing/invalid token on a protected route returns HTTP `401`.
- A missing book/record returns HTTP `404`.

### Standard error shapes

Validation error (`422`):
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

Unauthenticated (`401`):
```json
{ "message": "Unauthenticated." }
```

Forbidden (`403`):
```json
{ "message": "This action is unauthorized." }
```

---

## Endpoint index

| # | Method | Endpoint | Auth | Purpose |
|---|--------|----------|------|---------|
| 1 | POST | `/auth/register` | Public | Create an account, get a token |
| 2 | POST | `/auth/login` | Public | Log in, get a token |
| 3 | POST | `/auth/logout` | Protected | Revoke the current token |
| 4 | GET | `/auth/user` | Protected | Get the logged-in user |
| 5 | GET | `/genres` | Public | List all genres |
| 6 | GET | `/books` | Public | List/search/filter/sort books |
| 7 | GET | `/books/{slug}` | Public | Get one book's full detail |
| 8 | GET | `/books/{slug}/ratings` | Public | List a book's approved reviews |
| 9 | POST | `/books/{slug}/ratings` | Protected | Submit/update your review |
| 10 | DELETE | `/ratings/{id}` | Protected | Delete your own review |
| 11 | POST | `/books/{slug}/borrow` | Protected | Borrow a book |
| 12 | GET | `/borrows` | Protected | Your borrow history |
| 13 | POST | `/books/{slug}/bookmark` | Protected | Add/remove a bookmark (toggle) |
| 14 | GET | `/bookmarks` | Protected | Your bookmarked books |
| 15 | GET | `/dashboard` | Protected | Your stats, reviews, borrows |
| 16 | GET | `/recommendations` | Protected | Personalised book suggestions |
| 17 | GET | `/profile` | Protected | Get your profile |
| 18 | PATCH | `/profile` | Protected | Update name/email |
| 19 | PUT | `/profile/password` | Protected | Change password |

---

## 1. Register — `POST /auth/register`

**Auth:** Public
**When to use:** On the sign-up screen, to create a new student account. The response
includes a token, so the user is logged in immediately after registering.

**Request body**
```json
{
  "name": "Aisha Khan",
  "email": "aisha@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Rules**
- `name`: required, max 255 characters.
- `email`: required, valid email, lowercase, unique.
- `password`: required, must match `password_confirmation`, meets default strength rules.

**Success response — `201 Created`**
```json
{
  "data": {
    "id": 24,
    "name": "Aisha Khan",
    "email": "aisha@example.com",
    "role": "student",
    "student_id": null,
    "department": null,
    "avatar": null,
    "email_verified_at": null,
    "created_at": "2026-06-14T10:00:00+00:00"
  },
  "token": "12|a1b2c3d4e5f6g7h8i9j0..."
}
```

**Errors:** `422` if validation fails (e.g. email already taken).

---

## 2. Login — `POST /auth/login`

**Auth:** Public
**When to use:** On the login screen. Store the returned token securely on the device.

**Request body**
```json
{
  "email": "aisha@example.com",
  "password": "secret123"
}
```

**Success response — `200 OK`**
```json
{
  "data": {
    "id": 24,
    "name": "Aisha Khan",
    "email": "aisha@example.com",
    "role": "student",
    "student_id": null,
    "department": null,
    "avatar": null,
    "email_verified_at": null,
    "created_at": "2026-06-14T10:00:00+00:00"
  },
  "token": "13|x9y8z7w6v5u4..."
}
```

**Errors**
- `422` with `errors.email = ["These credentials do not match our records."]` for wrong email/password.
- `422` with a throttle message after 5 failed attempts (rate limited per email + IP).

---

## 3. Logout — `POST /auth/logout`

**Auth:** Protected
**When to use:** When the user taps "Log out". This deletes only the token used on this
device, so other devices stay logged in.

**Request body:** none.

**Success response — `200 OK`**
```json
{ "message": "Logged out." }
```

---

## 4. Current user — `GET /auth/user`

**Auth:** Protected
**When to use:** On app launch (with a stored token) to confirm the session is still valid
and load the user's info.

**Success response — `200 OK`**
```json
{
  "data": {
    "id": 24,
    "name": "Aisha Khan",
    "email": "aisha@example.com",
    "role": "student",
    "student_id": "STU-1024",
    "department": "CS",
    "avatar": null,
    "email_verified_at": null,
    "created_at": "2026-06-14T10:00:00+00:00"
  }
}
```

---

## 5. List genres — `GET /genres`

**Auth:** Public
**When to use:** To populate a genre filter dropdown on the catalogue screen.

**Success response — `200 OK`**
```json
{
  "data": [
    { "id": 1, "name": "Arts", "slug": "arts", "description": "Books about art.", "books_count": 8 },
    { "id": 2, "name": "Biology", "slug": "biology", "description": "Life sciences.", "books_count": 12 }
  ]
}
```

---

## 6. List books — `GET /books`

**Auth:** Public
**When to use:** The main catalogue/browse screen, and search results. Supports search,
genre filtering, minimum-rating filtering, sorting, and pagination (12 per page).

**Query parameters (all optional)**

| Param | Type | Description | Example |
|-------|------|-------------|---------|
| `q` | string | Search title, author, or ISBN | `q=harry` |
| `genre` | integer | Filter by genre id | `genre=3` |
| `rating` | integer (1–5) | Only books with approved average ≥ this | `rating=4` |
| `sort` | string | `newest` (default), `title`, or `rating` | `sort=rating` |
| `page` | integer | Page number | `page=2` |

**Example:** `GET /books?q=physics&genre=5&rating=4&sort=rating&page=1`

**Success response — `200 OK`**
```json
{
  "data": [
    {
      "id": 7,
      "title": "Quantum Foundations",
      "author": "R. Feynman",
      "isbn": "9781234567890",
      "slug": "quantum-foundations",
      "publisher": "Academic Press",
      "published_year": 2015,
      "description": "An introduction to quantum theory.",
      "cover_image": "covers/placeholder.jpg",
      "total_copies": 4,
      "available_copies": 2,
      "location_code": "A-12",
      "is_available": true,
      "average_rating": 4.5,
      "approved_ratings_count": 8,
      "genre": { "id": 5, "name": "Physics", "slug": "physics", "description": "Physics books." },
      "created_at": "2026-06-13T13:33:13+00:00"
    }
  ],
  "links": {
    "first": "https://.../api/v1/books?page=1",
    "last": "https://.../api/v1/books?page=5",
    "prev": null,
    "next": "https://.../api/v1/books?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 12,
    "to": 12,
    "total": 58
  }
}
```

**Notes:** `average_rating` and `approved_ratings_count` count **approved reviews only**.

---

## 7. Book detail — `GET /books/{slug}`

**Auth:** Public
**When to use:** The book detail screen. Use the book's `slug` (from the list) in the URL.

**Example:** `GET /books/quantum-foundations`

**Success response — `200 OK`**
```json
{
  "data": {
    "id": 7,
    "title": "Quantum Foundations",
    "author": "R. Feynman",
    "isbn": "9781234567890",
    "slug": "quantum-foundations",
    "publisher": "Academic Press",
    "published_year": 2015,
    "description": "An introduction to quantum theory.",
    "cover_image": "covers/placeholder.jpg",
    "total_copies": 4,
    "available_copies": 2,
    "location_code": "A-12",
    "is_available": true,
    "average_rating": 4.5,
    "approved_ratings_count": 8,
    "genre": { "id": 5, "name": "Physics", "slug": "physics", "description": "Physics books." },
    "created_at": "2026-06-13T13:33:13+00:00"
  }
}
```

**Errors:** `404` if the slug doesn't exist.

---

## 8. Book reviews (approved) — `GET /books/{slug}/ratings`

**Auth:** Public
**When to use:** The reviews section of the book detail screen. Returns only reviews that
staff have **approved** (15 per page, newest first).

**Success response — `200 OK`**
```json
{
  "data": [
    {
      "id": 41,
      "rating": 5,
      "message": "A brilliant read.",
      "is_approved": true,
      "user": { "id": 12, "name": "Omar Said" },
      "created_at": "2026-06-10T09:20:00+00:00",
      "updated_at": "2026-06-11T09:20:00+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "per_page": 15, "total": 8, "last_page": 1, "from": 1, "to": 8 }
}
```

---

## 9. Submit / update a review — `POST /books/{slug}/ratings`

**Auth:** Protected
**When to use:** When the user rates a book or edits their existing rating. Each user can
have only one review per book — sending again **updates** the existing one.
Every submission goes back into the **pending approval** queue (`is_approved` becomes
`false`) and won't appear in the public list until staff approve it.

**Request body**
```json
{
  "rating": 5,
  "message": "Loved this book."
}
```

**Rules**
- `rating`: required, integer 1–5.
- `message`: optional, max 1000 characters.

**Success response — `201 Created`** (new review) or **`200 OK`** (updated review)
```json
{
  "data": {
    "id": 88,
    "rating": 5,
    "message": "Loved this book.",
    "is_approved": false,
    "created_at": "2026-06-14T11:00:00+00:00",
    "updated_at": "2026-06-14T11:00:00+00:00"
  },
  "message": "Review submitted for approval."
}
```

**Errors:** `422` if rating is missing or out of range.

---

## 10. Delete your review — `DELETE /ratings/{id}`

**Auth:** Protected
**When to use:** When the user removes their own review. Use the review `id`.
A user can only delete **their own** review.

**Success response — `200 OK`**
```json
{ "message": "Review deleted." }
```

**Errors**
- `403` if the review belongs to another user.
- `404` if the review id doesn't exist.

---

## 11. Borrow a book — `POST /books/{slug}/borrow`

**Auth:** Protected
**When to use:** When the user taps "Borrow" on a book that has available copies.
Sets a 14-day due date and reduces the book's available copies by one.

**Request body:** none.

**Success response — `201 Created`**
```json
{
  "data": {
    "id": 55,
    "status": "active",
    "is_overdue": false,
    "borrowed_at": "2026-06-14T11:30:00+00:00",
    "due_date": "2026-06-28",
    "returned_at": null,
    "book": {
      "id": 7,
      "title": "Quantum Foundations",
      "slug": "quantum-foundations",
      "available_copies": 1,
      "genre": { "id": 5, "name": "Physics", "slug": "physics", "description": "Physics books." }
    },
    "created_at": "2026-06-14T11:30:00+00:00"
  }
}
```

**Errors**
- `422` `{ "message": "No copies are currently available." }` when the book is out of stock.

**Note:** Returning a book is handled by library staff (in the admin panel), so there is no
"return" endpoint in the mobile app.

---

## 12. Borrow history — `GET /borrows`

**Auth:** Protected
**When to use:** The user's "My Borrows" screen. Paginated (15 per page), newest first.
`status` is one of `active`, `returned`, `overdue`; `is_overdue` is a convenience flag.

**Success response — `200 OK`**
```json
{
  "data": [
    {
      "id": 55,
      "status": "active",
      "is_overdue": false,
      "borrowed_at": "2026-06-14T11:30:00+00:00",
      "due_date": "2026-06-28",
      "returned_at": null,
      "book": { "id": 7, "title": "Quantum Foundations", "slug": "quantum-foundations" },
      "created_at": "2026-06-14T11:30:00+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "per_page": 15, "total": 1, "last_page": 1, "from": 1, "to": 1 }
}
```

---

## 13. Toggle bookmark — `POST /books/{slug}/bookmark`

**Auth:** Protected
**When to use:** When the user taps the bookmark/heart icon. The same endpoint **adds** the
bookmark if it doesn't exist and **removes** it if it does. Use the returned `bookmarked`
flag to update the icon state.

**Request body:** none.

**Success response**
- `201 Created` when added:
```json
{ "bookmarked": true }
```
- `200 OK` when removed:
```json
{ "bookmarked": false }
```

---

## 14. Bookmarked books — `GET /bookmarks`

**Auth:** Protected
**When to use:** The "My Bookmarks" screen. Paginated (12 per page), newest first. Each item
includes the full book (with genre and approved-rating average).

**Success response — `200 OK`**
```json
{
  "data": [
    {
      "id": 30,
      "book": {
        "id": 7,
        "title": "Quantum Foundations",
        "author": "R. Feynman",
        "slug": "quantum-foundations",
        "cover_image": "covers/placeholder.jpg",
        "available_copies": 2,
        "is_available": true,
        "average_rating": 4.5,
        "approved_ratings_count": 8,
        "genre": { "id": 5, "name": "Physics", "slug": "physics", "description": "Physics books." }
      },
      "created_at": "2026-06-14T10:45:00+00:00"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "per_page": 12, "total": 1, "last_page": 1, "from": 1, "to": 1 }
}
```

---

## 15. Dashboard — `GET /dashboard`

**Auth:** Protected
**When to use:** The home/dashboard screen after login. Returns the user's quick stats, all
their reviews, and all their borrows.

**Success response — `200 OK`**
```json
{
  "stats": {
    "ratings_count": 5,
    "borrows_count": 3,
    "avg_rating_given": 4.2
  },
  "my_ratings": {
    "data": [
      {
        "id": 88,
        "rating": 5,
        "message": "Loved this book.",
        "is_approved": false,
        "book": { "id": 7, "title": "Quantum Foundations", "slug": "quantum-foundations" },
        "created_at": "2026-06-14T11:00:00+00:00",
        "updated_at": "2026-06-14T11:00:00+00:00"
      }
    ]
  },
  "my_borrows": {
    "data": [
      {
        "id": 55,
        "status": "active",
        "is_overdue": false,
        "borrowed_at": "2026-06-14T11:30:00+00:00",
        "due_date": "2026-06-28",
        "returned_at": null,
        "book": { "id": 7, "title": "Quantum Foundations", "slug": "quantum-foundations" },
        "created_at": "2026-06-14T11:30:00+00:00"
      }
    ]
  }
}
```

---

## 16. Recommendations — `GET /recommendations`

**Auth:** Protected
**When to use:** The "Recommended for you" section. Recommendations are generated daily by
the server. Choose which kind to show using the `type` parameter (matches the dashboard
tabs). Results are ordered by relevance score (highest first).

**Query parameter**

| Param | Values | Default | Description |
|-------|--------|---------|-------------|
| `type` | `collaborative`, `genre_based`, `trending` | `collaborative` | Recommendation strategy |

- `collaborative` — based on users with similar taste.
- `genre_based` — based on the user's favourite genres.
- `trending` — books popular recently.

**Example:** `GET /recommendations?type=trending`

**Success response — `200 OK`**
```json
{
  "data": [
    {
      "id": 200,
      "score": 0.92,
      "reason_type": "trending",
      "book": {
        "id": 14,
        "title": "Modern Algorithms",
        "author": "C. Cormen",
        "slug": "modern-algorithms",
        "cover_image": "covers/placeholder.jpg",
        "available_copies": 3,
        "is_available": true,
        "average_rating": 4.7,
        "approved_ratings_count": 15,
        "genre": { "id": 1, "name": "Computer Science", "slug": "computer-science", "description": "CS books." }
      },
      "created_at": "2026-06-14T03:00:00+00:00"
    }
  ]
}
```

**Note:** An empty `data` array means recommendations haven't been generated yet (they run
daily) — show a friendly placeholder.

---

## 17. Get profile — `GET /profile`

**Auth:** Protected
**When to use:** The profile/account screen.

**Success response — `200 OK`**
```json
{
  "data": {
    "id": 24,
    "name": "Aisha Khan",
    "email": "aisha@example.com",
    "role": "student",
    "student_id": "STU-1024",
    "department": "CS",
    "avatar": null,
    "email_verified_at": null,
    "created_at": "2026-06-14T10:00:00+00:00"
  }
}
```

---

## 18. Update profile — `PATCH /profile`

**Auth:** Protected
**When to use:** When the user edits their name or email. If the email changes, the account
becomes unverified again (`email_verified_at` resets to `null`).

**Request body**
```json
{
  "name": "Aisha R. Khan",
  "email": "aisha.khan@example.com"
}
```

**Rules**
- `name`: required, max 255.
- `email`: required, valid, lowercase, unique (the user's own current email is allowed).

**Success response — `200 OK`**
```json
{
  "data": {
    "id": 24,
    "name": "Aisha R. Khan",
    "email": "aisha.khan@example.com",
    "role": "student",
    "student_id": "STU-1024",
    "department": "CS",
    "avatar": null,
    "email_verified_at": null,
    "created_at": "2026-06-14T10:00:00+00:00"
  }
}
```

**Errors:** `422` if name/email invalid or email already used by someone else.

---

## 19. Change password — `PUT /profile/password`

**Auth:** Protected
**When to use:** The "Change password" form.

**Request body**
```json
{
  "current_password": "secret123",
  "password": "newSecret456",
  "password_confirmation": "newSecret456"
}
```

**Rules**
- `current_password`: required, must match the user's current password.
- `password`: required, must match `password_confirmation`, meets default strength rules.

**Success response — `200 OK`**
```json
{ "message": "Password updated." }
```

**Errors:** `422` if the current password is wrong or the new password is invalid.

---

## Typical mobile flow

1. **Launch:** if a token is stored, call `GET /auth/user` to validate it.
2. **Auth:** otherwise `POST /auth/register` or `POST /auth/login`, then store the token.
3. **Browse:** `GET /genres` for filters, `GET /books` for the catalogue.
4. **Detail:** `GET /books/{slug}` and `GET /books/{slug}/ratings`.
5. **Act:** `POST /books/{slug}/borrow`, `POST /books/{slug}/bookmark`,
   `POST /books/{slug}/ratings`.
6. **Personal:** `GET /dashboard`, `GET /recommendations`, `GET /borrows`, `GET /bookmarks`.
7. **Account:** `GET /profile`, `PATCH /profile`, `PUT /profile/password`.
8. **Exit:** `POST /auth/logout`.
