import Header from "./Header";
import HeroSection from "./HeroSection";

export default function Welcome() {
    const user = null;

    return (
        <div className="min-h-screen bg-white flex flex-col font-sans text-gray-900 antialiased">
            <Header user={user} />
            <HeroSection />
        </div>
    );
}
