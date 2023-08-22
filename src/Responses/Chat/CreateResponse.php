<?php

declare(strict_types=1);

namespace OpenAI\Responses\Chat;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Contracts\ResponseHasMetaInformationContract;
use OpenAI\Responses\Concerns\ArrayAccessible;
use OpenAI\Responses\Concerns\HasMetaInformation;
use OpenAI\Responses\ResponseMetaInformation;
use OpenAI\Testing\Responses\Concerns\Fakeable;

/**
 * @implements ResponseContract<array{id: string, object: string, created: int, model: string, choices: array<int, array{index: int, message: array{role: string, content: string|null, function_call?: array{name: string, arguments: string}}, finish_reason: string|null}>, usage: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}}>
 */
final class CreateResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /**
     * @use ArrayAccessible<array{id: string, object: string, created: int, model: string, choices: array<int, array{index: int, message: array{role: string, content: string|null, function_call?: array{name: string, arguments: string}}, finish_reason: string|null}>, usage: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}}>
     */
    use ArrayAccessible;

    use HasMetaInformation;
    use Fakeable;

    /**
     * @param  array<int, CreateResponseChoice>  $choices
     */
    private function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly int $created,
        public readonly string $model,
        public readonly array $choices,
        public readonly CreateResponseUsage $usage,
        private readonly ResponseMetaInformation $meta,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id: string, object: string, created: int, model: string, choices: array<int, array{index: int, message: array{role: string, content: ?string, function_call: ?array{name: string, arguments: string}}, finish_reason: string|null}>, usage: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}}  $attributes
     */
    public static function from(array $attributes, ResponseMetaInformation $meta): self
    {
        $choices = array_map(fn (array $result): CreateResponseChoice => CreateResponseChoice::from(
            $result
        ), $attributes['choices']);

        return new self(
            $attributes['id'],
            $attributes['object'],
            $attributes['created'],
            $attributes['model'],
            $choices,
            CreateResponseUsage::from($attributes['usage']),
            $meta,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'choices' => array_map(
                static fn (CreateResponseChoice $result): array => $result->toArray(),
                $this->choices,
            ),
            'usage' => $this->usage->toArray(),
        ];
    }
}
