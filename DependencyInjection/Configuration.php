<?php

namespace Rikudou\QrPaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root("rikudou_qr_payment");

        $rootNode
            ->children()
                ->arrayNode("cz")
                    ->children()
                        ->scalarNode("account")->end()
                        ->scalarNode("bankCode")->end()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->scalarNode("variableSymbol")->end()
                                ->scalarNode("specificSymbol")->end()
                                ->scalarNode("constantSymbol")->end()
                                ->scalarNode("currency")->end()
                                ->scalarNode("comment")->end()
                                ->integerNode("repeat")->end()
                                ->scalarNode("internalId")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("country")->end()
                                ->integerNode("dueDays")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("sk")
                    ->children()
                        ->scalarNode("account")->end()
                        ->scalarNode("bankCode")->end()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->scalarNode("variableSymbol")->end()
                                ->scalarNode("specificSymbol")->end()
                                ->scalarNode("constantSymbol")->end()
                                ->scalarNode("currency")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("internalId")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("country")->end()
                                ->scalarNode("swift")->end()
                                ->integerNode("dueDays")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("eu")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("de")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("at")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("nl")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("be")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("fi")
                    ->children()
                        ->scalarNode("iban")->end()
                        ->arrayNode("options")
                            ->children()
                                ->enumNode("character_set")->values([ "utf-8", "iso-8859-1", "iso-8859-2", "iso-8859-4", "iso-8859-5", "iso-8859-7", "iso-8859-10", "iso-8859-15" ])->end()
                                ->scalarNode("bic")->end()
                                ->scalarNode("swift")->end()
                                ->scalarNode("beneficiary_name")->end()
                                ->floatNode("amount")->end()
                                ->scalarNode("purpose")->end()
                                ->scalarNode("remittance_text")->end()
                                ->scalarNode("information")->end()
                                ->scalarNode("comment")->end()
                                ->scalarNode("currency")->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
