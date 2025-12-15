<?php

declare(strict_types=1);

namespace App\Tests\Unit\List;

use App\List\VideoGameList\Pagination;
use App\Model\ValueObject\Direction;
use App\Model\ValueObject\Sorting;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PaginationTest extends TestCase
{

    private Pagination $pagination;

    protected function setUp(): void
    {
        $this->pagination = new Pagination(
            page: 2,
            limit: 10,
            sorting: Sorting::Title, 
            direction: Direction::Descending
        );
    }

    public function testGetLastPageThrowsExceptionIfNotInitialized(): void
    {

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pagination is not initialized');

        $this->pagination->getLastPage();
    }
}
