<?php
/**
 * Route definition file
 */
declare(strict_types=1);

use FastRoute\RouteCollector;
use PhpMyAdmin\Controllers\AjaxController;
use PhpMyAdmin\Controllers\BrowseForeignersController;
use PhpMyAdmin\Controllers\ChangeLogController;
use PhpMyAdmin\Controllers\CheckRelationsController;
use PhpMyAdmin\Controllers\Database\CentralColumnsController;
use PhpMyAdmin\Controllers\Database\DataDictionaryController;
use PhpMyAdmin\Controllers\Database\DesignerController;
use PhpMyAdmin\Controllers\Database\EventsController;
use PhpMyAdmin\Controllers\Database\ExportController as DatabaseExportController;
use PhpMyAdmin\Controllers\Database\ImportController as DatabaseImportController;
use PhpMyAdmin\Controllers\Database\MultiTableQueryController;
use PhpMyAdmin\Controllers\Database\OperationsController;
use PhpMyAdmin\Controllers\Database\QueryByExampleController;
use PhpMyAdmin\Controllers\Database\RoutinesController;
use PhpMyAdmin\Controllers\Database\SearchController;
use PhpMyAdmin\Controllers\Database\SqlAutoCompleteController;
use PhpMyAdmin\Controllers\Database\SqlController as DatabaseSqlController;
use PhpMyAdmin\Controllers\Database\SqlFormatController;
use PhpMyAdmin\Controllers\Database\StructureController;
use PhpMyAdmin\Controllers\Database\TrackingController;
use PhpMyAdmin\Controllers\Database\TriggersController;
use PhpMyAdmin\Controllers\ErrorReportController;
use PhpMyAdmin\Controllers\ExportController;
use PhpMyAdmin\Controllers\GisDataEditorController;
use PhpMyAdmin\Controllers\HomeController;
use PhpMyAdmin\Controllers\ImportController;
use PhpMyAdmin\Controllers\ImportStatusController;
use PhpMyAdmin\Controllers\LicenseController;
use PhpMyAdmin\Controllers\LintController;
use PhpMyAdmin\Controllers\LogoutController;
use PhpMyAdmin\Controllers\NavigationController;
use PhpMyAdmin\Controllers\NormalizationController;
use PhpMyAdmin\Controllers\PhpInfoController;
use PhpMyAdmin\Controllers\Preferences\ExportController as PreferencesExportController;
use PhpMyAdmin\Controllers\Preferences\FeaturesController;
use PhpMyAdmin\Controllers\Preferences\ImportController as PreferencesImportController;
use PhpMyAdmin\Controllers\Preferences\MainPanelController;
use PhpMyAdmin\Controllers\Preferences\ManageController;
use PhpMyAdmin\Controllers\Preferences\NavigationController as PreferencesNavigationController;
use PhpMyAdmin\Controllers\Preferences\SqlController as PreferencesSqlController;
use PhpMyAdmin\Controllers\Preferences\TwoFactorController;
use PhpMyAdmin\Controllers\SchemaExportController;
use PhpMyAdmin\Controllers\Server\BinlogController;
use PhpMyAdmin\Controllers\Server\CollationsController;
use PhpMyAdmin\Controllers\Server\DatabasesController;
use PhpMyAdmin\Controllers\Server\EnginesController;
use PhpMyAdmin\Controllers\Server\ExportController as ServerExportController;
use PhpMyAdmin\Controllers\Server\ImportController as ServerImportController;
use PhpMyAdmin\Controllers\Server\PluginsController;
use PhpMyAdmin\Controllers\Server\PrivilegesController;
use PhpMyAdmin\Controllers\Server\ReplicationController;
use PhpMyAdmin\Controllers\Server\SqlController as ServerSqlController;
use PhpMyAdmin\Controllers\Server\Status\AdvisorController;
use PhpMyAdmin\Controllers\Server\Status\MonitorController;
use PhpMyAdmin\Controllers\Server\Status\ProcessesController;
use PhpMyAdmin\Controllers\Server\Status\QueriesController;
use PhpMyAdmin\Controllers\Server\Status\StatusController;
use PhpMyAdmin\Controllers\Server\Status\VariablesController as StatusVariables;
use PhpMyAdmin\Controllers\Server\UserGroupsController;
use PhpMyAdmin\Controllers\Server\VariablesController;
use PhpMyAdmin\Controllers\SqlController;
use PhpMyAdmin\Controllers\Table\AddFieldController;
use PhpMyAdmin\Controllers\Table\ChangeController;
use PhpMyAdmin\Controllers\Table\ChartController;
use PhpMyAdmin\Controllers\Table\CreateController;
use PhpMyAdmin\Controllers\Table\ExportController as TableExportController;
use PhpMyAdmin\Controllers\Table\FindReplaceController;
use PhpMyAdmin\Controllers\Table\GetFieldController;
use PhpMyAdmin\Controllers\Table\GisVisualizationController;
use PhpMyAdmin\Controllers\Table\ImportController as TableImportController;
use PhpMyAdmin\Controllers\Table\IndexesController;
use PhpMyAdmin\Controllers\Table\OperationsController as TableOperationsController;
use PhpMyAdmin\Controllers\Table\RecentFavoriteController;
use PhpMyAdmin\Controllers\Table\RelationController;
use PhpMyAdmin\Controllers\Table\ReplaceController;
use PhpMyAdmin\Controllers\Table\RowActionController;
use PhpMyAdmin\Controllers\Table\SearchController as TableSearchController;
use PhpMyAdmin\Controllers\Table\SqlController as TableSqlController;
use PhpMyAdmin\Controllers\Table\StructureController as TableStructureController;
use PhpMyAdmin\Controllers\Table\TrackingController as TableTrackingController;
use PhpMyAdmin\Controllers\Table\TriggersController as TableTriggersController;
use PhpMyAdmin\Controllers\Table\ZoomSearchController;
use PhpMyAdmin\Controllers\ThemesController;
use PhpMyAdmin\Controllers\TransformationOverviewController;
use PhpMyAdmin\Controllers\TransformationWrapperController;
use PhpMyAdmin\Controllers\UserPasswordController;
use PhpMyAdmin\Controllers\VersionCheckController;
use PhpMyAdmin\Controllers\ViewCreateController;
use PhpMyAdmin\Controllers\ViewOperationsController;

global $containerBuilder;

if (! defined('PHPMYADMIN')) {
    exit;
}

return function (RouteCollector $routes) use ($containerBuilder) {
    $routes->addGroup('', function (RouteCollector $routes) use ($containerBuilder) {
        /** @var HomeController $controller */
        $controller = $containerBuilder->get(HomeController::class);
        $routes->addRoute(['GET', 'POST'], '[/]', function () use ($controller) {
            $controller->index();
        });
        $routes->post('/set-theme', function () use ($controller) {
            $controller->setTheme();
        });
        $routes->post('/collation-connection', function () use ($controller) {
            $controller->setCollationConnection();
        });
        $routes->addRoute(['GET', 'POST'], '/recent-table', function () use ($controller) {
            $controller->reloadRecentTablesList();
        });
        $routes->addRoute(['GET', 'POST'], '/git-revision', function () use ($controller) {
            $controller->gitRevision();
        });
    });
    $routes->addGroup('/ajax', function (RouteCollector $routes) use ($containerBuilder) {
        /** @var AjaxController $controller */
        $controller = $containerBuilder->get(AjaxController::class);
        $routes->post('/list-databases', function () use ($controller) {
            $controller->databases();
        });
        $routes->post('/list-tables/{database}', function (array $vars) use ($controller) {
            $controller->tables($vars);
        });
        $routes->post('/list-columns/{database}/{table}', function (array $vars) use ($controller) {
            $controller->columns($vars);
        });
        $routes->post('/config-get', function () use ($controller) {
            $controller->getConfig();
        });
        $routes->post('/config-set', function () use ($controller) {
            $controller->setConfig();
        });
    });
    $routes->addRoute(['GET', 'POST'], '/browse-foreigners', function () use ($containerBuilder) {
        /** @var BrowseForeignersController $controller */
        $controller = $containerBuilder->get(BrowseForeignersController::class);
        $controller->index();
    });
    $routes->get('/changelog', function () use ($containerBuilder) {
        /** @var ChangeLogController $controller */
        $controller = $containerBuilder->get(ChangeLogController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/check-relations', function () use ($containerBuilder) {
        /** @var CheckRelationsController $controller */
        $controller = $containerBuilder->get(CheckRelationsController::class);
        $controller->index();
    });
    $routes->addGroup('/database', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/central-columns', function () use ($containerBuilder) {
            /** @var CentralColumnsController $controller */
            $controller = $containerBuilder->get(CentralColumnsController::class);
            $controller->index();
        });
        $routes->get('/data-dictionary/{database}', function (array $vars) use ($containerBuilder) {
            /** @var DataDictionaryController $controller */
            $controller = $containerBuilder->get(DataDictionaryController::class);
            $controller->index($vars);
        });
        $routes->addRoute(['GET', 'POST'], '/designer', function () use ($containerBuilder) {
            /** @var DesignerController $controller */
            $controller = $containerBuilder->get(DesignerController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/events', function () use ($containerBuilder) {
            /** @var EventsController $controller */
            $controller = $containerBuilder->get(EventsController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/export', function () use ($containerBuilder) {
            /** @var DatabaseExportController $controller */
            $controller = $containerBuilder->get(DatabaseExportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/import', function () use ($containerBuilder) {
            /** @var DatabaseImportController $controller */
            $controller = $containerBuilder->get(DatabaseImportController::class);
            $controller->index();
        });
        $routes->addGroup('/multi_table_query', function (RouteCollector $routes) use ($containerBuilder) {
            /** @var MultiTableQueryController $controller */
            $controller = $containerBuilder->get(MultiTableQueryController::class);
            $routes->get('', function () use ($controller) {
                $controller->index();
            });
            $routes->get('/tables', function () use ($controller) {
                $controller->table();
            });
            $routes->post('/query', function () use ($controller) {
                $controller->displayResults();
            });
        });
        $routes->addRoute(['GET', 'POST'], '/operations', function () use ($containerBuilder) {
            /** @var OperationsController $controller */
            $controller = $containerBuilder->get(OperationsController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/qbe', function () use ($containerBuilder) {
            /** @var QueryByExampleController $controller */
            $controller = $containerBuilder->get(QueryByExampleController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/routines', function () use ($containerBuilder) {
            /** @var RoutinesController $controller */
            $controller = $containerBuilder->get(RoutinesController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/search', function () use ($containerBuilder) {
            /** @var SearchController $controller */
            $controller = $containerBuilder->get(SearchController::class);
            $controller->index();
        });
        $routes->addGroup('/sql', function (RouteCollector $routes) use ($containerBuilder) {
            $routes->addRoute(['GET', 'POST'], '', function () use ($containerBuilder) {
                /** @var DatabaseSqlController $controller */
                $controller = $containerBuilder->get(DatabaseSqlController::class);
                $controller->index();
            });
            $routes->post('/autocomplete', function () use ($containerBuilder) {
                /** @var SqlAutoCompleteController $controller */
                $controller = $containerBuilder->get(SqlAutoCompleteController::class);
                $controller->index();
            });
            $routes->post('/format', function () use ($containerBuilder) {
                /** @var SqlFormatController $controller */
                $controller = $containerBuilder->get(SqlFormatController::class);
                $controller->index();
            });
        });
        $routes->addGroup('/structure', function (RouteCollector $routes) use ($containerBuilder) {
            /** @var StructureController $controller */
            $controller = $containerBuilder->get(StructureController::class);
            $routes->addRoute(['GET', 'POST'], '', function () use ($controller) {
                $controller->index();
            });
            $routes->addRoute(['GET', 'POST'], '/favorite-table', function () use ($controller) {
                $controller->addRemoveFavoriteTablesAction();
            });
            $routes->addRoute(['GET', 'POST'], '/real-row-count', function () use ($controller) {
                $controller->handleRealRowCountRequestAction();
            });
        });
        $routes->addRoute(['GET', 'POST'], '/tracking', function () use ($containerBuilder) {
            /** @var TrackingController $controller */
            $controller = $containerBuilder->get(TrackingController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/triggers', function () use ($containerBuilder) {
            /** @var TriggersController $controller */
            $controller = $containerBuilder->get(TriggersController::class);
            $controller->index();
        });
    });
    $routes->addRoute(['GET', 'POST'], '/error-report', function () use ($containerBuilder) {
        /** @var ErrorReportController $controller */
        $controller = $containerBuilder->get(ErrorReportController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/export', function () use ($containerBuilder) {
        /** @var ExportController $controller */
        $controller = $containerBuilder->get(ExportController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/gis-data-editor', function () use ($containerBuilder) {
        /** @var GisDataEditorController $controller */
        $controller = $containerBuilder->get(GisDataEditorController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/import', function () use ($containerBuilder) {
        /** @var ImportController $controller */
        $controller = $containerBuilder->get(ImportController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/import-status', function () use ($containerBuilder) {
        /** @var ImportStatusController $controller */
        $controller = $containerBuilder->get(ImportStatusController::class);
        $controller->index();
    });
    $routes->get('/license', function () use ($containerBuilder) {
        /** @var LicenseController $controller */
        $controller = $containerBuilder->get(LicenseController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/lint', function () use ($containerBuilder) {
        /** @var LintController $controller */
        $controller = $containerBuilder->get(LintController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/logout', function () use ($containerBuilder) {
        /** @var LogoutController $controller */
        $controller = $containerBuilder->get(LogoutController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/navigation', function () use ($containerBuilder) {
        /** @var NavigationController $controller */
        $controller = $containerBuilder->get(NavigationController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/normalization', function () use ($containerBuilder) {
        /** @var NormalizationController $controller */
        $controller = $containerBuilder->get(NormalizationController::class);
        $controller->index();
    });
    $routes->get('/phpinfo', function () use ($containerBuilder) {
        /** @var PhpInfoController $controller */
        $controller = $containerBuilder->get(PhpInfoController::class);
        $controller->index();
    });
    $routes->addGroup('/preferences', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/export', function () use ($containerBuilder) {
            /** @var PreferencesExportController $controller */
            $controller = $containerBuilder->get(PreferencesExportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/features', function () use ($containerBuilder) {
            /** @var FeaturesController $controller */
            $controller = $containerBuilder->get(FeaturesController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/import', function () use ($containerBuilder) {
            /** @var PreferencesImportController $controller */
            $controller = $containerBuilder->get(PreferencesImportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/main-panel', function () use ($containerBuilder) {
            /** @var MainPanelController $controller */
            $controller = $containerBuilder->get(MainPanelController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/manage', function () use ($containerBuilder) {
            /** @var ManageController $controller */
            $controller = $containerBuilder->get(ManageController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/navigation', function () use ($containerBuilder) {
            /** @var PreferencesNavigationController $controller */
            $controller = $containerBuilder->get(PreferencesNavigationController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/sql', function () use ($containerBuilder) {
            /** @var PreferencesSqlController $controller */
            $controller = $containerBuilder->get(PreferencesSqlController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/two-factor', function () use ($containerBuilder) {
            /** @var TwoFactorController $controller */
            $controller = $containerBuilder->get(TwoFactorController::class);
            $controller->index();
        });
    });
    $routes->addRoute(['GET', 'POST'], '/schema-export', function () use ($containerBuilder) {
        /** @var SchemaExportController $controller */
        $controller = $containerBuilder->get(SchemaExportController::class);
        $controller->index();
    });
    $routes->addGroup('/server', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/binlog', function () use ($containerBuilder) {
            /** @var BinlogController $controller */
            $controller = $containerBuilder->get(BinlogController::class);
            $controller->index();
        });
        $routes->get('/collations', function () use ($containerBuilder) {
            /** @var CollationsController $controller */
            $controller = $containerBuilder->get(CollationsController::class);
            $controller->index();
        });
        $routes->addGroup('/databases', function (RouteCollector $routes) use ($containerBuilder) {
            /** @var DatabasesController $controller */
            $controller = $containerBuilder->get(DatabasesController::class);
            $routes->addRoute(['GET', 'POST'], '', function () use ($controller) {
                $controller->index();
            });
            $routes->post('/create', function () use ($controller) {
                $controller->create();
            });
            $routes->post('/destroy', function () use ($controller) {
                $controller->destroy();
            });
        });
        $routes->addGroup('/engines', function (RouteCollector $routes) use ($containerBuilder) {
            /** @var EnginesController $controller */
            $controller = $containerBuilder->get(EnginesController::class);
            $routes->get('', function () use ($controller) {
                $controller->index();
            });
            $routes->get('/{engine}[/{page}]', function (array $vars) use ($controller) {
                $controller->show($vars);
            });
        });
        $routes->addRoute(['GET', 'POST'], '/export', function () use ($containerBuilder) {
            /** @var ServerExportController $controller */
            $controller = $containerBuilder->get(ServerExportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/import', function () use ($containerBuilder) {
            /** @var ServerImportController $controller */
            $controller = $containerBuilder->get(ServerImportController::class);
            $controller->index();
        });
        $routes->get('/plugins', function () use ($containerBuilder) {
            /** @var PluginsController $controller */
            $controller = $containerBuilder->get(PluginsController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/privileges', function () use ($containerBuilder) {
            /** @var PrivilegesController $controller */
            $controller = $containerBuilder->get(PrivilegesController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/replication', function () use ($containerBuilder) {
            /** @var ReplicationController $controller */
            $controller = $containerBuilder->get(ReplicationController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/sql', function () use ($containerBuilder) {
            /** @var ServerSqlController $controller */
            $controller = $containerBuilder->get(ServerSqlController::class);
            $controller->index();
        });
        $routes->addGroup('/status', function (RouteCollector $routes) use ($containerBuilder) {
            $routes->get('', function () use ($containerBuilder) {
                /** @var StatusController $controller */
                $controller = $containerBuilder->get(StatusController::class);
                $controller->index();
            });
            $routes->get('/advisor', function () use ($containerBuilder) {
                /** @var AdvisorController $controller */
                $controller = $containerBuilder->get(AdvisorController::class);
                $controller->index();
            });
            $routes->addGroup('/monitor', function (RouteCollector $routes) use ($containerBuilder) {
                /** @var MonitorController $controller */
                $controller = $containerBuilder->get(MonitorController::class);
                $routes->get('', function () use ($controller) {
                    $controller->index();
                });
                $routes->post('/chart', function () use ($controller) {
                    $controller->chartingData();
                });
                $routes->post('/slow-log', function () use ($controller) {
                    $controller->logDataTypeSlow();
                });
                $routes->post('/general-log', function () use ($controller) {
                    $controller->logDataTypeGeneral();
                });
                $routes->post('/log-vars', function () use ($controller) {
                    $controller->loggingVars();
                });
                $routes->post('/query', function () use ($controller) {
                    $controller->queryAnalyzer();
                });
            });
            $routes->addGroup('/processes', function (RouteCollector $routes) use ($containerBuilder) {
                /** @var ProcessesController $controller */
                $controller = $containerBuilder->get(ProcessesController::class);
                $routes->addRoute(['GET', 'POST'], '', function () use ($controller) {
                    $controller->index();
                });
                $routes->post('/refresh', function () use ($controller) {
                    $controller->refresh();
                });
                $routes->post('/kill/{id:\d+}', function (array $vars) use ($controller) {
                    $controller->kill($vars);
                });
            });
            $routes->get('/queries', function () use ($containerBuilder) {
                /** @var QueriesController $controller */
                $controller = $containerBuilder->get(QueriesController::class);
                $controller->index();
            });
            $routes->addRoute(['GET', 'POST'], '/variables', function () use ($containerBuilder) {
                /** @var StatusVariables $controller */
                $controller = $containerBuilder->get(StatusVariables::class);
                $controller->index();
            });
        });
        $routes->addRoute(['GET', 'POST'], '/user-groups', function () use ($containerBuilder) {
            /** @var UserGroupsController $controller */
            $controller = $containerBuilder->get(UserGroupsController::class);
            $controller->index();
        });
        $routes->addGroup('/variables', function (RouteCollector $routes) use ($containerBuilder) {
            /** @var VariablesController $controller */
            $controller = $containerBuilder->get(VariablesController::class);
            $routes->get('', function () use ($controller) {
                $controller->index();
            });
            $routes->get('/get/{name}', function (array $vars) use ($controller) {
                $controller->getValue($vars);
            });
            $routes->post('/set/{name}', function (array $vars) use ($controller) {
                $controller->setValue($vars);
            });
        });
    });
    $routes->addGroup('/sql', function (RouteCollector $routes) use ($containerBuilder) {
        /** @var SqlController $controller */
        $controller = $containerBuilder->get(SqlController::class);
        $routes->addRoute(['GET', 'POST'], '', function () use ($controller) {
            $controller->index();
        });
        $routes->post('/get-relational-values', function () use ($controller) {
            $controller->getRelationalValues();
        });
        $routes->post('/get-enum-values', function () use ($controller) {
            $controller->getEnumValues();
        });
        $routes->post('/get-set-values', function () use ($controller) {
            $controller->getSetValues();
        });
        $routes->get('/get-default-fk-check-value', function () use ($controller) {
            $controller->getDefaultForeignKeyCheckValue();
        });
        $routes->post('/set-column-preferences', function () use ($controller) {
            $controller->setColumnOrderOrVisibility();
        });
    });
    $routes->addGroup('/table', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/add-field', function () use ($containerBuilder) {
            /** @var AddFieldController $controller */
            $controller = $containerBuilder->get(AddFieldController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/change', function () use ($containerBuilder) {
            /** @var ChangeController $controller */
            $controller = $containerBuilder->get(ChangeController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/chart', function () use ($containerBuilder) {
            /** @var ChartController $controller */
            $controller = $containerBuilder->get(ChartController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/create', function () use ($containerBuilder) {
            /** @var CreateController $controller */
            $controller = $containerBuilder->get(CreateController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/export', function () use ($containerBuilder) {
            /** @var TableExportController $controller */
            $controller = $containerBuilder->get(TableExportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/find-replace', function () use ($containerBuilder) {
            /** @var FindReplaceController $controller */
            $controller = $containerBuilder->get(FindReplaceController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/get-field', function () use ($containerBuilder) {
            /** @var GetFieldController $controller */
            $controller = $containerBuilder->get(GetFieldController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/gis-visualization', function () use ($containerBuilder) {
            /** @var GisVisualizationController $controller */
            $controller = $containerBuilder->get(GisVisualizationController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/import', function () use ($containerBuilder) {
            /** @var TableImportController $controller */
            $controller = $containerBuilder->get(TableImportController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/indexes', function () use ($containerBuilder) {
            /** @var IndexesController $controller */
            $controller = $containerBuilder->get(IndexesController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/operations', function () use ($containerBuilder) {
            /** @var TableOperationsController $controller */
            $controller = $containerBuilder->get(TableOperationsController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/recent-favorite', function () use ($containerBuilder) {
            /** @var RecentFavoriteController $controller */
            $controller = $containerBuilder->get(RecentFavoriteController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/relation', function () use ($containerBuilder) {
            /** @var RelationController $controller */
            $controller = $containerBuilder->get(RelationController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/replace', function () use ($containerBuilder) {
            /** @var ReplaceController $controller */
            $controller = $containerBuilder->get(ReplaceController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/row-action', function () use ($containerBuilder) {
            /** @var RowActionController $controller */
            $controller = $containerBuilder->get(RowActionController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/search', function () use ($containerBuilder) {
            /** @var TableSearchController $controller */
            $controller = $containerBuilder->get(TableSearchController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/sql', function () use ($containerBuilder) {
            /** @var TableSqlController $controller */
            $controller = $containerBuilder->get(TableSqlController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/structure', function () use ($containerBuilder) {
            /** @var TableStructureController $controller */
            $controller = $containerBuilder->get(TableStructureController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/tracking', function () use ($containerBuilder) {
            /** @var TableTrackingController $controller */
            $controller = $containerBuilder->get(TableTrackingController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/triggers', function () use ($containerBuilder) {
            /** @var TableTriggersController $controller */
            $controller = $containerBuilder->get(TableTriggersController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/zoom-search', function () use ($containerBuilder) {
            /** @var ZoomSearchController $controller */
            $controller = $containerBuilder->get(ZoomSearchController::class);
            $controller->index();
        });
    });
    $routes->get('/themes', function () use ($containerBuilder) {
        /** @var ThemesController $controller */
        $controller = $containerBuilder->get(ThemesController::class);
        $controller->index();
    });
    $routes->addGroup('/transformation', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/overview', function () use ($containerBuilder) {
            /** @var TransformationOverviewController $controller */
            $controller = $containerBuilder->get(TransformationOverviewController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/wrapper', function () use ($containerBuilder) {
            /** @var TransformationWrapperController $controller */
            $controller = $containerBuilder->get(TransformationWrapperController::class);
            $controller->index();
        });
    });
    $routes->addRoute(['GET', 'POST'], '/user-password', function () use ($containerBuilder) {
        /** @var UserPasswordController $controller */
        $controller = $containerBuilder->get(UserPasswordController::class);
        $controller->index();
    });
    $routes->addRoute(['GET', 'POST'], '/version-check', function () use ($containerBuilder) {
        /** @var VersionCheckController $controller */
        $controller = $containerBuilder->get(VersionCheckController::class);
        $controller->index();
    });
    $routes->addGroup('/view', function (RouteCollector $routes) use ($containerBuilder) {
        $routes->addRoute(['GET', 'POST'], '/create', function () use ($containerBuilder) {
            /** @var ViewCreateController $controller */
            $controller = $containerBuilder->get(ViewCreateController::class);
            $controller->index();
        });
        $routes->addRoute(['GET', 'POST'], '/operations', function () use ($containerBuilder) {
            /** @var ViewOperationsController $controller */
            $controller = $containerBuilder->get(ViewOperationsController::class);
            $controller->index();
        });
    });
};
