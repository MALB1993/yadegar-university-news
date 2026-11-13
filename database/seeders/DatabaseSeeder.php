<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Every demo account uses the password "password".
     *
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private const STUDENTS = [
        ['دانشجوی نمونه', '40000001', 'student@example.com'],
        ['سارا محمدی', '40000002', 'sara@example.com'],
        ['امیر حسینی', '40000003', 'amir@example.com'],
        ['نگار رضوی', '40000004', 'negar@example.com'],
        ['محمد جعفری', '40000005', 'mohammad@example.com'],
    ];

    /**
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private const PROFESSORS = [
        ['دکتر علی احمدی', '10000001', 'professor@example.com'],
        ['دکتر مریم کریمی', '10000002', 'maryam@example.com'],
    ];

    /**
     * @var list<array{0: string, 1: string, 2: string}>
     */
    private const EMPLOYEES = [
        ['کارمند نمونه', '20000001', 'employee@example.com'],
    ];

    /**
     * @var list<string>
     */
    private const CATEGORIES = ['اطلاعیه‌ها', 'آموزشی', 'فرهنگی', 'پژوهشی', 'ورزشی'];

    /**
     * Each post names its category and writer by index.
     * Writers: 0 and 1 are professors, 2 is the employee.
     *
     * @var list<array{title: string, body: string, category: int, writer: int, days_ago: int, views: int, status?: string, is_pinned?: bool}>
     */
    private const POSTS = [
        [
            'title' => 'زمان ثبت‌نام ترم جدید اعلام شد',
            'body' => "ثبت‌نام ترم جدید از شنبه آغاز می‌شود و تا پایان هفته‌ی اول ادامه دارد.\n\nدانشجویان باید پیش از ثبت‌نام، بدهی شهریه‌ی خود را تسویه کرده باشند. جزئیات مراحل در سامانه‌ی آموزش قابل مشاهده است.",
            'category' => 0,
            'writer' => 2,
            'days_ago' => 1,
            'views' => 412,
            'is_pinned' => true,
        ],
        [
            'title' => 'تقویم آموزشی نیمسال پیش رو منتشر شد',
            'body' => "تقویم آموزشی شامل تاریخ شروع کلاس‌ها، امتحانات میان‌ترم و پایان‌ترم و تعطیلات رسمی است.\n\nلطفاً تقویم را دانلود و در برنامه‌ی خود ثبت کنید. تغییرات احتمالی از طریق همین سامانه اطلاع‌رسانی خواهد شد.",
            'category' => 0,
            'writer' => 2,
            'days_ago' => 3,
            'views' => 298,
            'is_pinned' => true,
        ],
        [
            'title' => 'کارگاه آشنایی با نرم‌افزار متلب برگزار می‌شود',
            'body' => "کارگاه دو روزه‌ی متلب برای دانشجویان مهندسی و علوم پایه برگزار می‌شود.\n\nمحورهای کارگاه شامل مبانی برنامه‌نویسی، رسم نمودار و حل معادلات است. ثبت‌نام در کارگاه رایگان است و ظرفیت محدود دارد.",
            'category' => 1,
            'writer' => 0,
            'days_ago' => 2,
            'views' => 185,
        ],
        [
            'title' => 'مهلت انتخاب واحد و حذف و اضافه',
            'body' => "انتخاب واحد و حذف و اضافه تا پایان هفته‌ی دوم انجام می‌شود.\n\nدانشجویان مشروطی پیش از انتخاب واحد باید با استاد راهنمای خود مشورت کنند و فرم مربوطه را تأیید کنند.",
            'category' => 1,
            'writer' => 1,
            'days_ago' => 5,
            'views' => 156,
        ],
        [
            'title' => 'جشن روز دانشجو با حضور اساتید برگزار می‌شود',
            'body' => "به مناسبت روز دانشجو، برنامه‌ی جشن در سالن همایش‌های دانشگاه برگزار می‌شود.\n\nبرنامه شامل سخنرانی، اجرای گروه‌های دانشجویی و نمایشگاه دستاوردهای انجمن‌های علمی است. حضور همه‌ی دانشجویان دعوت است.",
            'category' => 2,
            'writer' => 2,
            'days_ago' => 6,
            'views' => 231,
        ],
        [
            'title' => 'مسابقه‌ی کتاب‌خوانی دانشجویی',
            'body' => "کتابخانه‌ی مرکزی مسابقه‌ی کتاب‌خوانی تابستانه را برگزار می‌کند.\n\nدانشجویانی که تا پایان ماه بیشترین کتاب را مطالعه و معرفی کنند، جایزه دریافت خواهند کرد.",
            'category' => 2,
            'writer' => 1,
            'days_ago' => 9,
            'views' => 97,
        ],
        [
            'title' => 'فراخوان ارسال مقاله به همایش ملی مهندسی نرم‌افزار',
            'body' => "دانشکده‌ی فنی فراخوان ارسال مقاله به همایش ملی مهندسی نرم‌افزار را منتشر کرد.\n\nمقالات باید حداکثر تا ۱۵ روز دیگر از طریق سامانه‌ی پژوهشی دانشگاه ارسال شوند. قالب مقاله در پیوست‌های همین خبر موجود است.",
            'category' => 3,
            'writer' => 0,
            'days_ago' => 4,
            'views' => 143,
        ],
        [
            'title' => 'نشست علمی: کاربرد هوش مصنوعی در آموزش',
            'body' => "نشست علمی با موضوع کاربرد هوش مصنوعی در آموزش عالی برگزار می‌شود.\n\nدر این نشست، اساتید درباره‌ی ابزارهای یادگیری تطبیقی و چالش‌های اخلاقی آن گفت‌وگو خواهند کرد. ورود آزاد است.",
            'category' => 3,
            'writer' => 1,
            'days_ago' => 7,
            'views' => 120,
        ],
        [
            'title' => 'مسابقات فوتسال بین‌دانشکده‌ای',
            'body' => "مسابقات فوتسال بین‌دانشکده‌ای از هفته‌ی آینده در سالن ورزشی آغاز می‌شود.\n\nثبت‌نام تیم‌ها در دفتر تربیت بدنی انجام می‌شود. هر تیم باید حداقل ۷ بازیکن داشته باشد.",
            'category' => 4,
            'writer' => 2,
            'days_ago' => 8,
            'views' => 178,
        ],
        [
            'title' => 'راهنمای ثبت درخواست وام شهریه',
            'body' => "درخواست وام شهریه از طریق سامانه‌ی رفاه دانشجویی ثبت می‌شود.\n\nمدارک لازم شامل گواهی اشتغال به تحصیل و فیش حقوقی خانواده است. بررسی درخواست‌ها حدود دو هفته زمان می‌برد.",
            'category' => 1,
            'writer' => 1,
            'days_ago' => 11,
            'views' => 89,
        ],
        [
            'title' => 'تغییر ساعات کار کتابخانه‌ی مرکزی',
            'body' => "این متن هنوز پیش‌نویس است و منتشر نشده است.\n\nساعت کار جدید کتابخانه پس از تأیید مدیریت اعلام خواهد شد.",
            'category' => 0,
            'writer' => 0,
            'days_ago' => 0,
            'views' => 0,
            'status' => 'draft',
        ],
    ];

    /**
     * Each comment names the post by index, the author as either a student
     * (index into the students list) or a writer (index into the writers list),
     * and the body.
     *
     * @var list<array{post: int, author: 'student'|'writer', index: int, body: string}>
     */
    private const COMMENTS = [
        ['post' => 0, 'author' => 'student', 'index' => 0, 'body' => 'ممنون از اطلاع‌رسانی به موقع. ثبت‌نام از کجا باید انجام شود؟'],
        ['post' => 0, 'author' => 'student', 'index' => 1, 'body' => 'آیا بدهی شهریه با کارت بانکی قابل تسویه است؟'],
        ['post' => 0, 'author' => 'writer', 'index' => 2, 'body' => 'ثبت‌نام از طریق سامانه‌ی آموزش و بخش «انتخاب واحد» انجام می‌شود.'],
        ['post' => 2, 'author' => 'student', 'index' => 2, 'body' => 'ظرفیت کارگاه تمام شد یا هنوز جا داریم؟'],
        ['post' => 2, 'author' => 'student', 'index' => 3, 'body' => 'ممنون، ثبت‌نام کردم. کارگاه خیلی مفید به نظر می‌رسد.'],
        ['post' => 4, 'author' => 'student', 'index' => 0, 'body' => 'جشن امسال خیلی بهتر از سال قبل برگزار می‌شود؟'],
        ['post' => 6, 'author' => 'student', 'index' => 4, 'body' => 'قالب مقاله در کدام بخش سامانه قرار دارد؟'],
        ['post' => 8, 'author' => 'student', 'index' => 1, 'body' => 'آیا تیم‌های خانم‌ها هم می‌توانند شرکت کنند؟'],
        ['post' => 8, 'author' => 'writer', 'index' => 2, 'body' => 'بله، بخش بانوان جداگانه برگزار می‌شود.'],
        ['post' => 9, 'author' => 'student', 'index' => 2, 'body' => 'مدارک را باید حضوری تحویل داد یا آنلاین کافی است؟'],
        ['post' => 3, 'author' => 'student', 'index' => 4, 'body' => 'اگر مشروط هستم، با چه کسی باید مشورت کنم؟'],
    ];

    /**
     * Seed the application's database with demo accounts and news.
     */
    public function run(): void
    {
        $students = $this->createUsers(self::STUDENTS, 'student');
        $professors = $this->createUsers(self::PROFESSORS, 'professor');
        $employees = $this->createUsers(self::EMPLOYEES, 'employee');

        $writers = $professors->concat($employees)->values();
        $categories = collect(self::CATEGORIES)
            ->map(fn (string $name) => Category::create(['name' => $name]))
            ->values();

        $posts = $this->createPosts($writers, $categories);

        $this->createComments($posts, $students, $writers);

        $students[0]->bookmarkedPosts()->attach([$posts[0]->id, $posts[2]->id, $posts[6]->id]);
        $students[1]->bookmarkedPosts()->attach([$posts[4]->id, $posts[8]->id]);

        $students[0]->followedCategories()->attach([$categories[0]->id, $categories[1]->id]);
        $students[1]->followedCategories()->attach([$categories[3]->id]);

        $students[0]->likedComments()->attach(Comment::query()->take(3)->pluck('id'));
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string}>  $rows
     * @return Collection<int, User>
     */
    private function createUsers(array $rows, string $role): Collection
    {
        return collect($rows)->map(fn (array $row) => User::factory()->create([
            'fullname' => $row[0],
            'identification_code' => $row[1],
            'email' => $row[2],
            'role' => $role,
        ]));
    }

    /**
     * @param  Collection<int, User>  $writers
     * @param  Collection<int, Category>  $categories
     * @return Collection<int, Post>
     */
    private function createPosts(Collection $writers, Collection $categories): Collection
    {
        return collect(self::POSTS)->map(function (array $definition) use ($writers, $categories): Post {
            $post = Post::factory()->create([
                'user_id' => $writers[$definition['writer']]->id,
                'category_id' => $categories[$definition['category']]->id,
                'title' => $definition['title'],
                'body' => $definition['body'],
                'status' => $definition['status'] ?? 'published',
                'is_pinned' => $definition['is_pinned'] ?? false,
            ]);

            $post->forceFill([
                'views' => $definition['views'],
                'created_at' => now()->subDays($definition['days_ago']),
            ])->save();

            return $post;
        });
    }

    /**
     * @param  Collection<int, Post>  $posts
     * @param  Collection<int, User>  $students
     * @param  Collection<int, User>  $writers
     */
    private function createComments(Collection $posts, Collection $students, Collection $writers): void
    {
        foreach (self::COMMENTS as $definition) {
            $author = $definition['author'] === 'student'
                ? $students[$definition['index']]
                : $writers[$definition['index']];

            $comment = new Comment(['body' => $definition['body']]);
            $comment->user()->associate($author);
            $posts[$definition['post']]->comments()->save($comment);
        }
    }
}
