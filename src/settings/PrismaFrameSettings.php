<?php

namespace GreenWix\prismaFrame\settings;

final class PrismaFrameSettings {

  public bool $debug;

  /** @var string[] */
  public array $supportedApiVersions;

  /**
   * @param string[] $supportedApiVersions
   */
  public function __construct(bool $debug, array $supportedApiVersions) {
    $this->debug = $debug;
    $this->supportedApiVersions = $supportedApiVersions;
  }

}