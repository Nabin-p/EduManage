<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AcademicSetting
 *
 * @property int $id
 * @property string $attendance_type
 * @property int $is_final_marks_submitted
 * @property string $marks_submission_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\AcademicSettingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereAttendanceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereIsFinalMarksSubmitted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereMarksSubmissionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AcademicSetting whereUpdatedAt($value)
 */
	class AcademicSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AssignedTeacher
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $semester_id
 * @property int $class_id
 * @property int $section_id
 * @property int $course_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @property-read \App\Models\User|null $teacher
 * @method static \Database\Factories\AssignedTeacherFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedTeacher whereUpdatedAt($value)
 */
	class AssignedTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Assignment
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $semester_id
 * @property int $class_id
 * @property int $section_id
 * @property int $course_id
 * @property int $session_id
 * @property string $assignment_name
 * @property string $assignment_file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @property-read \App\Models\User|null $teacher
 * @method static \Database\Factories\AssignmentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereAssignmentFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereAssignmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereUpdatedAt($value)
 */
	class Assignment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $student_id
 * @property string $attendance_date
 * @property string $status
 * @property int $session_id
 * @property string $marked_by
 * @property string|null $check_in_time
 * @property int|null $course_id
 * @property int|null $class_id
 * @property int|null $section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\AttendanceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAttendanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCheckInTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereMarkedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Book
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $isbn
 * @property string|null $description
 * @property int $total_copies
 * @property int $available_copies
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BookIssue[] $issues
 * @property-read int|null $issues_count
 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAvailableCopies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereIsbn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTotalCopies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUpdatedAt($value)
 */
	class Book extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BookIssue
 *
 * @property int $id
 * @property int $book_id
 * @property int $student_id
 * @property string $issue_date
 * @property string $due_date
 * @property string|null $return_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book $book
 * @property-read \App\Models\User $student
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookIssue whereUpdatedAt($value)
 */
	class BookIssue extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Course
 *
 * @property int $id
 * @property string $course_name
 * @property string $course_type
 * @property int $class_id
 * @property int $semester_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CourseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $title
 * @property string $start
 * @property string $end
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\EventFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Exam
 *
 * @property int $id
 * @property string $exam_name
 * @property string $start_date
 * @property string $end_date
 * @property int $class_id
 * @property int $course_id
 * @property int $semester_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Semester|null $semester
 * @method static \Database\Factories\ExamFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 */
	class Exam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamRule
 *
 * @property int $id
 * @property float $total_marks
 * @property float $pass_marks
 * @property string $marks_distribution_note
 * @property int $exam_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ExamRuleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereMarksDistributionNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule wherePassMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamRule whereUpdatedAt($value)
 */
	class ExamRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FinalMark
 *
 * @property int $id
 * @property float $calculated_marks
 * @property float $final_marks
 * @property string|null $note
 * @property int $student_id
 * @property int $class_id
 * @property int $section_id
 * @property int $course_id
 * @property int $semester_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\FinalMarkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark query()
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereCalculatedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereFinalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FinalMark whereUpdatedAt($value)
 */
	class FinalMark extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradeRule
 *
 * @property int $id
 * @property float $point
 * @property string $grade
 * @property float $start_at
 * @property float $end_at
 * @property int $grading_system_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GradingSystem|null $gradingSystem
 * @method static \Database\Factories\GradeRuleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereGradingSystemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeRule whereUpdatedAt($value)
 */
	class GradeRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradingSystem
 *
 * @property int $id
 * @property string $system_name
 * @property int $class_id
 * @property int $semester_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Semester|null $semester
 * @method static \Database\Factories\GradingSystemFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereSystemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradingSystem whereUpdatedAt($value)
 */
	class GradingSystem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mark
 *
 * @property int $id
 * @property float $marks
 * @property int $student_id
 * @property int $class_id
 * @property int $section_id
 * @property int $course_id
 * @property int $exam_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Exam|null $exam
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\MarkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mark query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mark whereUpdatedAt($value)
 */
	class Mark extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notice
 *
 * @property int $id
 * @property string $notice
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\NoticeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notice whereUpdatedAt($value)
 */
	class Notice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Promotion
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_id
 * @property int $section_id
 * @property int $session_id
 * @property string $id_card_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\PromotionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereUpdatedAt($value)
 */
	class Promotion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Routine
 *
 * @property int $id
 * @property string $start
 * @property string $end
 * @property int $weekday
 * @property int $class_id
 * @property int $section_id
 * @property int $course_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @property-read \App\Models\Section|null $section
 * @method static \Database\Factories\RoutineFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Routine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Routine query()
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Routine whereWeekday($value)
 */
	class Routine extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SchoolClass
 *
 * @property int $id
 * @property string $class_name
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Syllabus[] $syllabi
 * @property-read int|null $syllabi_count
 * @method static \Database\Factories\SchoolClassFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolClass whereUpdatedAt($value)
 */
	class SchoolClass extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SchoolSession
 *
 * @property int $id
 * @property string $session_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SchoolSessionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession whereSessionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolSession whereUpdatedAt($value)
 */
	class SchoolSession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $section_name
 * @property string $room_no
 * @property int $class_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SchoolClass|null $schoolClass
 * @method static \Database\Factories\SectionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereRoomNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Semester
 *
 * @property int $id
 * @property string $semester_name
 * @property string $start_date
 * @property string $end_date
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SemesterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester query()
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereSemesterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Semester whereUpdatedAt($value)
 */
	class Semester extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentAcademicInfo
 *
 * @property int $id
 * @property string|null $board_reg_no
 * @property int $student_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $fee
 * @property int $is_paid
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\StudentAcademicInfoFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereBoardRegNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAcademicInfo whereUpdatedAt($value)
 */
	class StudentAcademicInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentParentInfo
 *
 * @property int $id
 * @property int $student_id
 * @property string $father_name
 * @property string $father_phone
 * @property string $mother_name
 * @property string $mother_phone
 * @property string $parent_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $student
 * @method static \Database\Factories\StudentParentInfoFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereFatherPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereMotherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereMotherPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereParentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentParentInfo whereUpdatedAt($value)
 */
	class StudentParentInfo extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Syllabus
 *
 * @property int $id
 * @property string $syllabus_name
 * @property string $syllabus_file_path
 * @property int $class_id
 * @property int $course_id
 * @property int $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\SyllabusFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus query()
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereSyllabusFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereSyllabusName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Syllabus whereUpdatedAt($value)
 */
	class Syllabus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $gender
 * @property string $phone
 * @property string $address
 * @property string $address2
 * @property string $city
 * @property string $zip
 * @property string|null $photo
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StudentAcademicInfo|null $academic_info
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BookIssue[] $bookIssues
 * @property-read int|null $book_issues_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mark[] $marks
 * @property-read int|null $marks_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\StudentParentInfo|null $parent_info
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereZip($value)
 */
	class User extends \Eloquent {}
}

