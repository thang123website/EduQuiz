# High-Concurrency Quiz API Optimization Plan

This document outlines the step-by-step implementation plan to build and optimize the **Student Quiz API** for the EduQuiz system, specifically designed to handle massive concurrency (e.g., 100,000 students taking a multiple-choice exam simultaneously).

## 1. Goal & Architectural Shift

When 100k students start a quiz and submit answers concurrently, direct synchronous MySQL operations will result in connection pool exhaustion, database deadlocks, and severe latency. 

We must shift from a **Synchronous DB-Driven Flow** to an **Asynchronous Event-Driven & Caching Flow** using Redis and Laravel Queues.

## 2. API Flow & Step-by-Step Implementation

### Step 1: Read Optimization (Fetching Quiz Data)
When 100k students open a quiz, fetching questions and options directly from MySQL is inefficient.
- [ ] **Cache Quiz Data**: When an admin publishes a quiz, serialize and store the quiz structure (questions, options, without correct answers) in Redis: `quiz:{quiz_id}:content`.
- [ ] **Student Fetch API**: Create `GET /api/v1/quizzes/{quiz_id}/start`. This endpoint fetches the quiz data directly from Redis (O(1) time complexity) and returns it to the frontend.
- [ ] **Init Attempt**: Dispatch a lightweight Job to insert the `quiz_attempts` record asynchronously, returning a generated `attempt_id` instantly to the user.

### Step 2: Write Optimization (Auto-Saving User Responses)
As students click answers, saving each click to MySQL immediately will overwhelm the database.
- [ ] **Redis Buffer**: Create `POST /api/v1/attempts/{attempt_id}/responses`.
- [ ] Instead of `UserResponse::create(...)`, store the answer in Redis using a Hash: `HSET attempt:{attempt_id}:responses {question_id} {option_id}`.
- [ ] Redis handles this entirely in-memory, processing 100k+ requests per second without locking MySQL.

### Step 3: Batch Processing (Syncing to MySQL)
We need to ensure data persistence without overloading the DB.
- [ ] **Scheduled Worker**: Create a Laravel command or Job (e.g., `SyncResponsesToDatabase`) that runs every minute.
- [ ] The worker scans Redis for modified attempts, pulls the hashes, and performs **Bulk Inserts/Updates** into the `user_responses` table using `upsert()` or `insertOnDuplicateKey`.
- [ ] This reduces 100,000 individual queries down to a few batch operations.

### Step 4: Async Grading (Submitting the Quiz)
Calculating scores synchronously when a user clicks "Submit" is CPU-heavy and slows down the response.
- [ ] **Submit API**: Create `POST /api/v1/attempts/{attempt_id}/submit`.
- [ ] When called, the API immediately marks the attempt as `processing` in Redis and returns a success response (`{"status": "processing"}`).
- [ ] **Dispatch Grading Job**: The API dispatches a Laravel Queue Job (e.g., `GradeQuizAttemptJob`).
- [ ] **Background Worker**: The Job retrieves user answers from Redis, compares them against the correct answers (also cached or queried efficiently), calculates the final score, and updates the `quiz_attempts` table.
- [ ] **SSE / Polling**: The frontend can poll or use Server-Sent Events (SSE) to get the final score once grading is complete.

### Step 5: API Security & Key Configuration
To prevent unauthorized access to the high-concurrency API from unknown clients and mitigate DDoS/bot attacks:
- [ ] **Admin Configuration UI**: Implement an "API Settings" section in the admin dashboard (Settings > System) with an option to Enable/Disable the REST API and a field to generate/view a shared secret **API Key**.
- [ ] **API Middleware**: Create an `ApiKeyMiddleware` to protect all API routes.
- [ ] **X-API-KEY Header Verification**: Ensure the middleware checks for the `X-API-KEY` header in every request. It should validate this header against the configured key (cached in Redis for fast lookup) before allowing the request to proceed to Sanctum authentication or controllers.

## 3. Required Code Components to Build

### [NEW] `routes/api.php`
- `GET /quizzes/{id}/start`
- `POST /attempts/{id}/responses`
- `POST /attempts/{id}/submit`
- *Note: All routes must be protected by the new `verify.api.key` middleware.*

### [NEW] Middleware
- `App\Http\Middleware\VerifyApiKeyMiddleware`

### [NEW] Controllers
- `App\Http\Controllers\Api\QuizController`
- `App\Http\Controllers\Api\AttemptController`

### [NEW] Jobs & Commands
- `App\Jobs\InitQuizAttemptJob`
- `App\Jobs\GradeQuizAttemptJob`
- `App\Console\Commands\SyncRedisResponsesToDB`

## 4. Advanced Database Optimization (MySQL)
- [ ] Ensure `user_responses` uses a composite primary/unique key on `(attempt_id, question_id)` to facilitate fast upserts.
- [ ] Partition the `user_responses` table by month/year if data grows into tens of millions of rows.

## 5. Verification & Testing Plan

### Load Testing
- [ ] Configure Apache JMeter or K6 to simulate 10,000 concurrent users performing a sequence: Start Quiz -> Answer 50 questions -> Submit Quiz.
- [ ] Verify that API latency for auto-saving responses remains strictly under 50ms.

### Functional Verification
- [ ] Ensure that background synchronization properly handles edge cases (e.g., Redis server restart, duplicate submissions).
- [ ] Verify score calculation accuracy after background grading is complete.
