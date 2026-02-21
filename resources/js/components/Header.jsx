function NavLink({ href, children }) {
    return (
        <a
            href={href}
            className="px-6 py-2 text-gray-700 hover:text-gray-900 font-medium transition"
        >
            {children}
        </a>
    );
}

export default function Header({ user }) {
    return (
        <header className="w-full px-8 py-6 bg-white border-b border-gray-200">
            <div className="flex items-center justify-between">
                <div>
                    <a href="/">
                        <img
                            src="/images/dentsusoken_logo.png"
                            alt="DENTSUSOKEN_LOGO"
                            className="h-11"
                        />
                    </a>
                </div>

                <nav className="flex items-center gap-4">
                    {user ? (
                        <NavLink href="/dashboard">ダッシュボード</NavLink>
                    ) : (
                        <>
                            <NavLink href="/login">ログイン</NavLink>
                            <NavLink href="/register">新規登録</NavLink>
                        </>
                    )}
                </nav>
            </div>
        </header>
    );
}
