export default function Header({ user }) {
    return (
        <header className="w-full px-8 py-6 bg-white border-b border-gray-200">
            <div className="flex items-center justify-between">
                <div>
                    <img
                        src="/images/dentsusoken_logo.png"
                        alt="DENTSUSOKEN_LOGO"
                        className="h-11"
                    />
                </div>

                <nav className="flex items-center gap-4">
                    {user ? (
                        <a
                            href="/dashboard"
                            className="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                        >
                            ダッシュボード
                        </a>
                    ) : (
                        <>
                            <a
                                href="/login"
                                className="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                            >
                                ログイン
                            </a>
                            <a
                                href="/register"
                                className="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
                            >
                                新規登録
                            </a>
                        </>
                    )}
                </nav>
            </div>
        </header>
    );
}
