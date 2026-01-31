<?php
// app/core/Router.php
class Router {
    public static function route($path) {
        switch ($path) {
            case 'login':
                require '../app/controllers/AuthController.php';
                AuthController::login();
                break;
            case 'anggota/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AnggotaController.php';
                AnggotaController::create();
                break;
            case 'anggota/list':
                Auth::requireLogin();
                require '../app/controllers/AnggotaController.php';
                AnggotaController::list();
                break;
            case 'simpanan/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SimpananController.php';
                SimpananController::create();
                break;
            case 'simpanan/list':
                Auth::requireLogin();
                require '../app/controllers/SimpananController.php';
                SimpananController::list();
                break;
            case 'pinjaman/create':
                Auth::requireLogin();
                require '../app/controllers/PinjamanController.php';
                PinjamanController::create();
                break;
            case 'pinjaman/approve':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/PinjamanController.php';
                PinjamanController::approve();
                break;
            case 'pinjaman/list':
                Auth::requireLogin();
                require '../app/controllers/PinjamanController.php';
                PinjamanController::list();
                break;
            case 'angsuran/bayar':
                Auth::requireLogin();
                require '../app/controllers/AngsuranController.php';
                AngsuranController::bayar();
                break;
            case 'shu/generate':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ShuController.php';
                ShuController::generate();
                break;
            case 'shu/list':
                Auth::requireLogin();
                require '../app/controllers/ShuController.php';
                ShuController::list();
                break;
            case 'rat/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/RatController.php';
                RatController::create();
                break;
            case 'rat/sahkan':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/RatController.php';
                RatController::sahkan();
                break;
            // Produk routes
            case 'produk/list':
                Auth::requireLogin();
                require '../app/controllers/ProdukController.php';
                ProdukController::list();
                break;
            case 'produk/detail':
                Auth::requireLogin();
                require '../app/controllers/ProdukController.php';
                ProdukController::detail();
                break;
            case 'produk/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::create();
                break;
            case 'produk/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::update();
                break;
            case 'produk/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::delete();
                break;
            // Order routes
            case 'order/list':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::list();
                break;
            case 'order/detail':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::detail();
                break;
            case 'order/create':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::create();
                break;
            case 'order/updateStatus':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/OrderController.php';
                OrderController::updateStatus();
                break;
            // Cart routes
            case 'cart/list':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::list();
                break;
            case 'cart/add':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::add();
                break;
            case 'cart/updateQty':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::updateQty();
                break;
            case 'cart/remove':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::remove();
                break;
            case 'cart/clear':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::clear();
                break;
            // Supplier routes
            case 'supplier/list':
                Auth::requireLogin();
                require '../app/controllers/SupplierController.php';
                SupplierController::list();
                break;
            case 'supplier/detail':
                Auth::requireLogin();
                require '../app/controllers/SupplierController.php';
                SupplierController::detail();
                break;
            case 'supplier/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::create();
                break;
            case 'supplier/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::update();
                break;
            case 'supplier/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::delete();
                break;
            // Investor routes
            case 'investor/list':
                Auth::requireLogin();
                require '../app/controllers/InvestorController.php';
                InvestorController::list();
                break;
            case 'investor/detail':
                Auth::requireLogin();
                require '../app/controllers/InvestorController.php';
                InvestorController::detail();
                break;
            case 'investor/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::create();
                break;
            case 'investor/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::update();
                break;
            case 'investor/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::delete();
                break;
            // Agent routes
            case 'agent/list':
                Auth::requireLogin();
                require '../app/controllers/AgentController.php';
                AgentController::list();
                break;
            case 'agent/detail':
                Auth::requireLogin();
                require '../app/controllers/AgentController.php';
                AgentController::detail();
                break;
            case 'agent/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::create();
                break;
            case 'agent/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::update();
                break;
            case 'agent/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::delete();
                break;
            // Accounting routes - Chart of Accounts
            case 'accounting/coa/list':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::coaList();
                break;
            case 'accounting/coa/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::coaCreate();
                break;
            case 'accounting/coa/detail':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::coaDetail();
                break;
            case 'accounting/coa/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::coaUpdate();
                break;
            case 'accounting/coa/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::coaDelete();
                break;
            // Accounting routes - Journal Entries
            case 'accounting/jurnal/list':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::jurnalList();
                break;
            case 'accounting/jurnal/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::jurnalCreate();
                break;
            case 'accounting/jurnal/detail':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::jurnalDetail();
                break;
            // Accounting routes - General Ledger
            case 'accounting/bukuBesar':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::bukuBesar();
                break;
            case 'accounting/neracaSaldo':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::neracaSaldo();
                break;
            case 'accounting/labaRugi':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::labaRugi();
                break;
            // Accounting routes - Fixed Assets
            case 'accounting/asset/list':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::assetList();
                break;
            case 'accounting/asset/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::assetCreate();
                break;
            case 'accounting/asset/detail':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::assetDetail();
                break;
            case 'accounting/asset/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::assetUpdate();
                break;
            case 'accounting/asset/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::assetDelete();
                break;
            case 'accounting/asset/calculateDepreciation':
                Auth::requireLogin();
                require '../app/controllers/AccountingController.php';
                AccountingController::assetCalculateDepreciation();
                break;
            case 'accounting/asset/saveDepreciation':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AccountingController.php';
                AccountingController::assetSaveDepreciation();
                break;
            // Dashboard Analytics routes
            case 'dashboard/stats':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getStats();
                break;
            case 'dashboard/simpananTrend':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getSimpananTrend();
                break;
            case 'dashboard/pinjamanStats':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getPinjamanStats();
                break;
            case 'dashboard/angsuranStats':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getAngsuranStats();
                break;
            case 'dashboard/shuHistory':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getShuHistory();
                break;
            case 'dashboard/financialOverview':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getFinancialOverview();
                break;
            case 'dashboard/recentActivities':
                Auth::requireLogin();
                require '../app/controllers/DashboardController.php';
                DashboardController::getRecentActivities();
                break;
            // Notification routes
            case 'notification/sendTunggakan':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/NotificationController.php';
                NotificationController::sendTunggakanManual();
                break;
            case 'notification/processAllTunggakan':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/NotificationController.php';
                NotificationController::processAllTunggakan();
                break;
            case 'notification/broadcastShu':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/NotificationController.php';
                NotificationController::broadcastShu();
                break;
            case 'notification/emailLogs':
                Auth::requireLogin();
                require '../app/controllers/NotificationController.php';
                NotificationController::getEmailLogs();
                break;
            case 'notification/testEmail':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/NotificationController.php';
                NotificationController::testEmail();
                break;
            // In-App Notification routes
            case 'notifikasi/myNotifications':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::getMyNotifications();
                break;
            case 'notifikasi/unreadCount':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::getUnreadCount();
                break;
            case 'notifikasi/markAsRead':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::markAsRead();
                break;
            case 'notifikasi/markAllAsRead':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::markAllAsRead();
                break;
            case 'notifikasi/dismiss':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::dismiss();
                break;
            case 'notifikasi/getPreferences':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::getPreferences();
                break;
            case 'notifikasi/updatePreferences':
                Auth::requireLogin();
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::updatePreferences();
                break;
            case 'notifikasi/all':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/NotifikasiController.php';
                NotifikasiController::getAllNotifications();
                break;
            // Security Audit routes
            case 'security/runAudit':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SecurityController.php';
                SecurityController::runAudit();
                break;
            case 'security/getAuditLogs':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SecurityController.php';
                SecurityController::getAuditLogs();
                break;
            case 'security/getSettings':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SecurityController.php';
                SecurityController::getSecuritySettings();
                break;
            case 'security/updateSetting':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SecurityController.php';
                SecurityController::updateSecuritySetting();
                break;
            case 'security/getCSRFToken':
                Auth::requireLogin();
                require '../app/controllers/SecurityController.php';
                SecurityController::getCSRFToken();
                break;
            case 'security/generateReport':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SecurityController.php';
                SecurityController::generateSecurityReport();
                break;
            case 'security/test':
                // WARNING: This endpoint is for testing only - REMOVE IN PRODUCTION
                require '../app/controllers/SecurityController.php';
                SecurityController::testEndpoint();
                break;
            // MFA (Multi-Factor Authentication) routes
            case 'mfa/enable':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::enableMFA();
                break;
            case 'mfa/disable':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::disableMFA();
                break;
            case 'mfa/status':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::getMFAStatus();
                break;
            case 'mfa/sendChallenge':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::sendMFAChallenge();
                break;
            case 'mfa/verify':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::verifyMFA();
                break;
            case 'mfa/qrCode':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::getQRCode();
                break;
            case 'mfa/backupCodes':
                Auth::requireLogin();
                require '../app/controllers/MFAController.php';
                MFAController::generateBackupCodes();
                break;
            case 'mfa/settings':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/MFAController.php';
                MFAController::getMFASettings();
                break;
            case 'mfa/forceEnable':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/MFAController.php';
                MFAController::forceEnableMFA();
                break;
            case 'mfa/forceDisable':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/MFAController.php';
                MFAController::forceDisableMFA();
                break;
            case 'mfa/cleanExpired':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/MFAController.php';
                MFAController::cleanExpiredCodes();
                break;
            // Backup Management routes
            case 'backup/stats':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::getBackupStats();
                break;
            case 'backup/run':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::runManualBackup();
                break;
            case 'backup/list':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::listBackupFiles();
                break;
            case 'backup/download':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::downloadBackup();
                break;
            case 'backup/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::deleteBackup();
                break;
            case 'backup/restore':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::restoreBackup();
                break;
            case 'backup/checkPermissions':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/BackupController.php';
                BackupController::checkBackupPermissions();
                break;
            // Reporting & Export routes
            case 'reporting/getAvailableReports':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::getAvailableReports();
                break;
            case 'reporting/exportAnggotaCSV':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::exportAnggotaCSV();
                break;
            case 'reporting/exportSimpananCSV':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::exportSimpananCSV();
                break;
            case 'reporting/exportPinjamanCSV':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::exportPinjamanCSV();
                break;
            case 'reporting/generatePDFReport':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::generatePDFReport();
                break;
            case 'reporting/scheduleReport':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ReportingController.php';
                ReportingController::scheduleReport();
                break;
            // Voting System routes
            case 'voting/getRATAgendas':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getRATAgendas();
                break;
            case 'voting/getRATAgenda':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getRATAgenda();
                break;
            case 'voting/createRATAgenda':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/VotingController.php';
                VotingController::createRATAgenda();
                break;
            case 'voting/updateRATAgenda':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/VotingController.php';
                VotingController::updateRATAgenda();
                break;
            case 'voting/getVotingTopics':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getVotingTopics();
                break;
            case 'voting/createVotingTopic':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/VotingController.php';
                VotingController::createVotingTopic();
                break;
            case 'voting/submitVote':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::submitVote();
                break;
            case 'voting/getResults':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getVotingResults();
                break;
            case 'voting/checkVoteStatus':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::checkVoteStatus();
                break;
            case 'voting/registerForRAT':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::registerForRAT();
                break;
            case 'voting/getParticipants':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/VotingController.php';
                VotingController::getRATParticipants();
                break;
            case 'voting/markAttended':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/VotingController.php';
                VotingController::markAttended();
                break;
            case 'voting/getStats':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getVotingStats();
                break;
            case 'voting/getActiveVotings':
                Auth::requireLogin();
                require '../app/controllers/VotingController.php';
                VotingController::getActiveVotings();
                break;
            // Forum routes
            case 'forum/getCategories':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getCategories();
                break;
            case 'forum/getThreads':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getThreads();
                break;
            case 'forum/getThread':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getThread();
                break;
            case 'forum/createThread':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::createThread();
                break;
            case 'forum/createPost':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::createPost();
                break;
            case 'forum/search':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::search();
                break;
            case 'forum/subscribe':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::subscribe();
                break;
            case 'forum/unsubscribe':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::unsubscribe();
                break;
            case 'forum/checkSubscription':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::checkSubscription();
                break;
            case 'forum/getStats':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getStats();
                break;
            case 'forum/getUserProfile':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getUserProfile();
                break;
            case 'forum/reportPost':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::reportPost();
                break;
            case 'forum/getSubscriptions':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getSubscriptions();
                break;
            case 'forum/markAsRead':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::markAsRead();
                break;
            case 'forum/getRecentActivity':
                Auth::requireLogin();
                require '../app/controllers/ForumController.php';
                ForumController::getRecentActivity();
                break;
            default:
                echo json_encode(['status' => false, 'message' => 'Endpoint not found']);
        }
    }
}
?>
