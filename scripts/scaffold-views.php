<?php

$base = dirname(__DIR__).'/resources/views';
$stub = <<<'BLADE'
@extends('layouts.app')
@section('title', '%s')
@section('content')
<x-guide title="Module guide" :items="%s" />
<div class="card ls-card"><div class="card-body"><h1 class="h4">%s</h1><p class="text-muted small mb-0">LearnSphere module page — use the actions on this screen to manage your workflow.</p></div></div>
@endsection
BLADE;

$pages = [
    'admin/users/index' => ['Users', '["Search and paginate accounts.","Create or edit users and assign a single role.","Suspend accounts instead of deleting when unsure."]'],
    'admin/users/create' => ['Create user', '["Set email and temporary password.","Pick exactly one role slug.","Default status should be active for staff invites."]'],
    'admin/users/edit' => ['Edit user', '["Update status to suspend access instantly.","Role changes replace the previous assignment.","Leave password blank to keep the current hash."]'],
    'admin/categories/index' => ['Categories', '["Sort order controls catalogue filters.","Inactive categories hide from new courses.","Delete only empty categories."]'],
    'admin/categories/create' => ['New category', '["Slug is generated from the name.","Add a short description for learners.","Set sort order for display priority."]'],
    'admin/categories/edit' => ['Edit category', '["Renaming updates the slug.","Toggle status instead of deleting when courses exist.","Keep sort order consistent."]'],
    'admin/moderation/index' => ['Course moderation', '["Filter by draft, in_review, or published.","Publish sets published_at timestamp.","Archive removes courses from the catalogue."]'],
    'admin/reports/index' => ['Reports', '["Enrollment totals are grouped per course.","Completion counts use completed_at on enrollments.","Revenue sums paid simulated orders only."]'],
    'admin/audit/index' => ['Audit log', '["Each row is an immutable admin action.","Use entity id to trace related records.","IP and agent aid security reviews."]'],
    'content/moderation/index' => ['Publish courses', '["Content managers may publish reviewed courses.","Draft and in_review items appear here.","Publishing is logged in the audit trail."]'],
    'support/users/index' => ['Support — users', '["Read-only directory of learners and staff.","Search by email prefix.","Open a user to view enrollments."]'],
    'support/users/show' => ['User detail', '["View roles and enrollment history.","Do not edit records from this desk.","Escalate account issues to admins."]'],
    'support/enrollments/index' => ['Support — enrollments', '["Recent enrollments across the platform.","Verify payment vs free enrollment type.","Cross-check learner complaints here."]'],
    'instructor/courses/index' => ['My courses', '["Create drafts and build sections on the course hub.","Submit for review when content is ready.","Super admins can publish directly."]'],
    'instructor/courses/create' => ['New course', '["Choose category and pricing.","Free courses use price 0.","Save as draft before adding lessons."]'],
    'instructor/courses/edit' => ['Edit course', '["Update marketing copy and metadata.","Price changes affect new checkouts only.","Keep level and language accurate."]'],
    'instructor/question-banks/index' => ['Question banks', '["Banks are scoped to this course.","Add questions before attaching to quizzes.","Delete unused banks to reduce clutter."]'],
    'instructor/quizzes/index' => ['Quizzes', '["Attach questions from banks on the edit screen.","Set pass percentage and attempt limits.","Publish when learners should see the quiz."]'],
    'instructor/quizzes/edit' => ['Edit quiz', '["Select questions to attach with default marks.","Shuffle and results flags control learner UX.","Save status as published for the player."]'],
    'instructor/assignments/index' => ['Assignments', '["Set due dates and max marks.","Open submissions list to grade work.","Allow late submission when appropriate."]'],
    'instructor/assignments/edit' => ['Edit assignment', '["Instructions support HTML snippets.","Closing status stops new submissions.","Link aligns to a course section."]'],
    'instructor/assignments/submissions' => ['Submissions', '["Review text and uploaded files.","Award marks up to max_marks.","Feedback is visible to the learner."]'],
    'instructor/announcements/index' => ['Announcements', '["Posted announcements notify enrolled learners.","Keep titles concise for notification feeds.","Remove outdated posts when needed."]'],
    'instructor/live-sessions/index' => ['Live sessions', '["External meeting URL only — no built-in video.","Schedule start/end in your timezone.","Share the join link in the course hub."]'],
    'instructor/discussions/index' => ['Discussion moderation', '["Threads scoped to courses you teach.","Super admins see all courses.","Pin or moderate inappropriate posts via status."]'],
    'instructor/analytics/show' => ['Course analytics', '["Enrollment count includes active seats.","Completions require all published lessons done.","Use insights to improve engagement."]'],
    'learner/catalogue/index' => ['Catalogue', '["Filter by category or search keywords.","Only published courses appear here.","Open a course to enroll or checkout."]'],
    'learner/catalogue/show' => ['Course detail', '["Preview lessons may be visible before enroll.","Free enroll starts learning immediately.","Paid courses redirect to simulated checkout."]'],
    'learner/quizzes/take' => ['Take quiz', '["Submit before leaving — attempts are limited.","Objective questions auto-grade on submit.","Short answers may need manual review later."]'],
    'learner/quizzes/result' => ['Quiz result', '["Pass/fail follows quiz pass percentage.","Review scores when show_results is enabled.","Retake only if attempts remain."]'],
    'learner/assignments/submit' => ['Submit assignment', '["Provide text and/or upload a file under 5MB.","Resubmitting replaces your prior attempt.","Watch due dates and late policies."]'],
    'learner/discussions/index' => ['Discussions', '["Start a thread or reply to classmates.","Stay on topic for the course community.","Instructors may moderate threads."]'],
    'learner/certificates/index' => ['Certificates', '["Issued after course completion rules pass.","Share verification codes publicly.","Download links appear when PDFs exist."]'],
    'learner/certificates/show' => ['Certificate', '["Verification code proves authenticity.","Course title and issue date are shown.","Keep your learner profile name accurate."]'],
    'learner/orders/index' => ['Order history', '["Simulated payments only — no real cards.","Paid orders should grant enrollment access.","Contact support if status looks wrong."]'],
    'learner/orders/checkout' => ['Checkout', '["Confirm course price in INR.","Place order runs deterministic simulation.","Success enrolls you automatically."]'],
    'learner/paths/index' => ['Learning paths', '["Paths sequence multiple courses.","Complete courses in suggested order.","Published paths only are listed."]'],
    'learner/paths/show' => ['Learning path', '["Follow linked courses in sort order.","Enroll in each course individually.","Track progress on your dashboard."]'],
    'learner/notifications/index' => ['Notifications', '["Derived from announcements, grades, and certs.","Mark items read to tidy your inbox.","Session stores read state locally."]'],
];

foreach ($pages as $path => [$title, $items]) {
    $dir = dirname($base.'/'.$path);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $file = $base.'/'.$path.'.blade.php';
    if (file_exists($file)) {
        continue;
    }
    file_put_contents($file, sprintf($stub, $title, $items, $title));
}

echo "Scaffolded ".count($pages)." view stubs.\n";
