<?php

declare(strict_types=1);

namespace Pingen\Support;

trait HasBatchContext
{
    protected string $batchId;

    public function setBatchId(string $batchId): self
    {
        $this->batchId = $batchId;

        return $this;
    }

    public function getBatchId(): string
    {
        if (! $this->batchId) {
            throw new \RuntimeException('Batch id has to be set first.'); //@codeCoverageIgnore
        }

        return $this->batchId;
    }
}
