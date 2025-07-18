<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ParseBooksJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses books from a JSON resource and updates the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        try {
            $response = Http::get(config('constants.data_sources.books_json_url'));
            $booksData = $response->json();
        } catch (\Exception $e) {
            $this->error('Failed to fetch JSON data: '.$e->getMessage());

            return Command::FAILURE;
        }

        if (empty($booksData)) {
            $this->info('No data found in the JSON resource.');

            return Command::SUCCESS;
        }

        $this->info('Starting book data parsing and update...');
        $this->parseBooks($booksData);
        $this->info('Book data parsing and update completed successfully.');

        return Command::SUCCESS;
    }

    private function parseBooks(array $booksData): void
    {
        foreach ($booksData as $bookData) {
            $isbn = $bookData['isbn'] ?? null;
            if (! $isbn) {
                $this->warn('Skipping book with missing ISBN: '.json_encode($bookData));

                continue;
            }

            $date = date('Y-m-d', strtotime($bookData['publishedDate']['$date'] ?? null));

            /** @var Book $book */
            $book = Book::firstOrNew(['isbn' => $isbn]);
            $book->title = $bookData['title'] ?? 'N/A';
            $book->description = $bookData['shortDescription'] ?? null;
            $book->status = $bookData['status'] ?? null;
            $book->published_date = isset($bookData['publishedDate']['$date']) ? $date : null;
            $book->save();

            $this->handleAuthors($bookData, $book);
            $this->handleCategories($bookData, $book);

            $this->info("Processed book: {$book->title} (ISBN: {$book->isbn})");
        }
    }

    private function handleAuthors(array $bookData, Book $book): void
    {
        if (! isset($bookData['authors']) || ! is_array($bookData['authors'])) {
            return;
        }

        $authorIds = [];
        foreach ($bookData['authors'] as $authorName) {
            $authorName = trim($authorName);
            if (empty($authorName)) {
                $this->warn("Skipping empty author name for book: {$book->title} (ISBN: {$book->isbn})");

                continue;
            }

            /** @var Author $author */
            $author = Author::firstOrCreate(['name' => $authorName]);
            $authorIds[] = $author->id;
        }

        $book->authors()->sync($authorIds);
    }

    private function handleCategories(array $bookData, Book $book): void
    {
        if (! isset($bookData['categories']) || ! is_array($bookData['categories'])) {
            return;
        }

        $categoryIds = [];
        foreach ($bookData['categories'] as $categoryName) {
            $categoryName = trim($categoryName);
            if (empty($categoryName)) {
                $this->warn("Skipping empty category name for book: {$book->title} (ISBN: {$book->isbn})");

                continue;
            }

            /** @var Category $category */
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $categoryIds[] = $category->id;
        }

        $book->categories()->sync($categoryIds);
    }
}
