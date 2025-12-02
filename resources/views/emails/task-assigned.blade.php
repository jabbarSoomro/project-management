<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Assigned</title>
</head>
<body>
<h1>New Task Assigned</h1>
<p>Hello {{ $task->assignedUser->name }},</p>
<p>You have been assigned a new task:</p>
<ul>
    <li><strong>Title:</strong> {{ $task->title }}</li>
    <li><strong>Project:</strong> {{ $task->project->title }}</li>
    <li><strong>Deadline:</strong> {{ $task->deadline->format('F d, Y') }}</li>
    <li><strong>Status:</strong> {{ $task->status }}</li>
</ul>
<p>Please login to the system to view more details.</p>
</body>
</html>
*/
