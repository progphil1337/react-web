<?php
declare(strict_types=1);

namespace yoummday;
use RuntimeException;

final class Puzzle
{

    private const JSON_IDENTIFIER = 'JSON:';
    private const NEW_LINE = "\n";

    private int $step = 1;
    /** @var array<string,\Closure> */
    private readonly array $operatorMap;

    public function __construct(
        public readonly string $url,
    )
    {
        $this->operatorMap = [
            'ADD' => fn(array $p) => (int)$p[0] + (int)$p[1],
            'XOR' => fn(array $p) => (int)$p[0] ^ (int)$p[1],
            'MD5' => fn(array $p) => md5($p[0]),
            'CURL' => function (array $p): int {
                $response = $this->curl($this->url . $p[0]);
                $result = json_decode($response);

                return $result->answer;
            },
            'MAILTO' => function (array $p): void {
                echo 'Bewerbung senden';
                exit(0);
            }
        ];
    }

    public function solve(string $content): void
    {
        $json = $this->getJson($content);
        $taskInfo = $this->getTaskInfo($json->task);

        if (!array_key_exists($taskInfo['op'], $this->operatorMap)) {
            throw new RuntimeException(sprintf('Operation %s not found: %s%s', $taskInfo['op'], PHP_EOL, $content));
        }

        echo sprintf('Solving %s...', $taskInfo['op']) . PHP_EOL;

        $result = $this->curl($this->url, [
            'token' => $json->token,
            'robot' => 'on',
            'answer' => $this->operatorMap[$taskInfo['op']]($taskInfo['payload'])
        ]);

        $this->step++;

        if ($this->step === 6) {
            echo '6/6 solved';
        } else {
            $this->solve($result);
        }
    }

    /**
     * @param array<string,mixed> $post
     */
    private function curl(string $url, array $post = []): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if (count($post) !== 0) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new RuntimeException('cURL request failed');
        }

        return $response;
    }


    /**
     * @return array<string, string|array<string>>
     */
    private function getTaskInfo(string $task): array
    {
        $split = explode(':', trim($task));

        return [
            'op' => $split[0],
            // remove empty values and reindex entries
            'payload' => array_values(array_filter(explode(' ', $split[1]), fn(string $v) => $v !== ''))
        ];
    }

    private function getJson(string $content): object
    {
        $json = (object)[];
        foreach (explode(self::NEW_LINE, $content) as $line) {
            if (str_starts_with($line, self::JSON_IDENTIFIER)) {
                $json = json_decode(str_replace(self::JSON_IDENTIFIER, '', $line));
                break;
            }
        }

        return $json;
    }
}

$puzzle = new Puzzle('http://95.217.79.100:1042/');
$puzzle->solve(file_get_contents($puzzle->url));
