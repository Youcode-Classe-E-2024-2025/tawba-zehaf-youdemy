<?php $title = 'Manage Courses'; ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Manage Courses</h1>
        <div class="mb-4">
            <a href="/admin/courses/create" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Create New Course
            </a>
        </div>
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <?= $course->getId() ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <?= htmlspecialchars($course->getTitle()) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <?= htmlspecialchars($course->getTeacher()->getUsername()) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <?= htmlspecialchars($course->getCategory()->getName()) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            $<?= number_format($course->getPrice(), 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <?= $course->isPublished() ? 'Published' : 'Draft' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <a href="/admin/courses/<?= $course->getId() ?>/edit" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                            <a href="/admin/courses/<?= $course->getId() ?>/toggle" class="text-<?= $course->isPublished() ? 'red' : 'green' ?>-600 hover:text-<?= $course->isPublished() ? 'red' : 'green' ?>-900">
                                <?= $course->isPublished() ? 'Unpublish' : 'Publish' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

