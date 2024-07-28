import Image from "next/image";

export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div className="relative" id="home">
          <div aria-hidden="true" className="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
              <div className="blur-[106px] h-56 bg-gradient-to-br from-primary to-purple-400 dark:from-blue-700"></div>
              <div className="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
          </div>
          <div className="relative pt-36 ml-auto">
              <div className="lg:w-2/3 text-center mx-auto">
                  <h1 className="text-gray-900 dark:text-white font-bold text-5xl md:text-6xl xl:text-7xl">Unintrusive cross-platform solution to compnent analytics.</h1>
                  <p className="mt-8 text-gray-700 dark:text-gray-300">componentWatch simplifies the process of configuring component analytics by abstracting your analytics away from everything under the hood empowing your content editors build optimised web pages</p>
                  <div className="mt-16 flex flex-wrap justify-center gap-y-4 gap-x-6">
                      <a
                        href="mailto:hello@componentwatch.tech?subject=Registering%20my%20interest%20&body=I'm%20interested%20for%20a%20demo%20of%20componentWatch"
                        className="relative flex h-11 w-full items-center justify-center px-6 before:absolute before:inset-0 before:rounded-full before:border before:border-transparent before:bg-primary/10 before:bg-gradient-to-b before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 dark:before:border-gray-700 dark:before:bg-gray-800 sm:w-max"
                      >
                        <span
                          className="relative text-base font-semibold text-primary dark:text-white"
                          >Register your interest</span
                        >
                      </a>
                  </div>
                  <div className="hidden py-8 mt-16 border-y border-gray-100 dark:border-gray-800 sm:flex justify-between">
                      <div className="text-left">
                          <h6 className="text-lg font-semibold text-gray-700 dark:text-white">Flexibility</h6>
                          <p className="mt-2 text-gray-500">Custom rulesets for identifying components</p>
                      </div>
                      <div className="text-left">
                          <h6 className="text-lg font-semibold text-gray-700 dark:text-white">Intergration</h6>
                          <p className="mt-2 text-gray-500">Intergration with google analytics reporting</p>
                      </div>
                      <div className="text-left">
                          <h6 className="text-lg font-semibold text-gray-700 dark:text-white">Lightweight</h6>
                          <p className="mt-2 text-gray-500">Engineered from the begining with green computing in mind</p>
                      </div>
                  </div>
              </div>    
          </div>
          <div className="flex min-h-screen flex-col items-center justify-between">
            <Image src="/Design.png" width="3840" height="2160" alt="screenshot of componentWatch Dashboard Alpha"/>
          </div>
      </div>
    </main>
  );
}
