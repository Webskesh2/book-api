<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\Author;

class ParseBooksJson extends Command
{
    private const BOOKS_URL = 'https://raw.githubusercontent.com/bvaughn/infinite-list-reflow-examples/refs/heads/master/books.json';

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
            $this->error('Failed to fetch JSON data: ' . $e->getMessage());

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

    /**
     * @param array $booksData
     * @return void
     */
    private function parseBooks(array $booksData): void
    {
        foreach ($booksData as $bookData) {
            $isbn = $bookData['isbn'] ?? null;
            if (!$isbn) {
                $this->warn('Skipping book with missing ISBN: ' . json_encode($bookData));
                continue;
            }

            $book = Book::firstOrNew(['isbn' => $isbn]);
            $book->title = $bookData['title'] ?? 'N/A';
            $book->description = $bookData['description'] ?? null;
            $book->published_date = isset($bookData['publishedDate']['$date'])
                ? date('Y-m-d', strtotime($bookData['publishedDate']['$date']))
                : null;

            $book->save();
            $this->handleAuthors($bookData, $book);
            $this->info("Processed book: {$book->title} (ISBN: {$book->isbn})");
        }
    }

    /**
     * @param array $bookData
     * @param Book $book
     * @return void
     */
    private function handleAuthors(array $bookData, Book $book): void
    {
        if (isset($bookData['authors']) && is_array($bookData['authors'])) {
            $authorIds = [];
            foreach ($bookData['authors'] as $authorName) {
                $authorName = trim($authorName);
                if (empty($authorName)) {
                    $this->warn("Skipping empty author name for book: {$book->title} (ISBN: {$book->isbn})");
                    continue;
                }

                $author = Author::firstOrCreate(['name' => $authorName]);
                $authorIds[] = $author->id;
            }

            $book->authors()->sync($authorIds); // Sync authors to handle additions/removals
        } else {
            $book->authors()->detach(); // Detach all authors if none provided
        }
    }
}
