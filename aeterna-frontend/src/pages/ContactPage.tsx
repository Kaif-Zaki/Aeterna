// import { useState } from "react";
// import { z } from "zod";
// import { toast } from "sonner";
// import AnimatedSection from "@/components/AnimatedSection";
// import PageTransition from "@/components/PageTransition";
// import { Instagram, Twitter, MapPin, Mail, Send } from "lucide-react";

// const contactSchema = z.object({
//   name: z.string().trim().min(1, "Name is required").max(100),
//   email: z.string().trim().email("Invalid email address").max(255),
//   message: z.string().trim().min(1, "Message is required").max(2000),
// });

// const ContactPage = () => {
//   const [form, setForm] = useState({ name: "", email: "", message: "" });
//   const [errors, setErrors] = useState<Record<string, string>>({});
//   const [sending, setSending] = useState(false);

//   const handleSubmit = (e: React.FormEvent) => {
//     e.preventDefault();
//     const result = contactSchema.safeParse(form);
//     if (!result.success) {
//       const fieldErrors: Record<string, string> = {};
//       result.error.errors.forEach((err) => {
//         if (err.path[0]) fieldErrors[err.path[0] as string] = err.message;
//       });
//       setErrors(fieldErrors);
//       return;
//     }
//     setErrors({});
//     setSending(true);

//     // Store in localStorage (connect Lovable Cloud for persistence)
//     const messages = JSON.parse(localStorage.getItem("contact_messages") || "[]");
//     messages.push({ ...result.data, created_at: new Date().toISOString() });
//     localStorage.setItem("contact_messages", JSON.stringify(messages));

//     setTimeout(() => {
//       setSending(false);
//       setForm({ name: "", email: "", message: "" });
//       toast.success("Message sent successfully. We'll be in touch.");
//     }, 800);
//   };

//   return (
//     <PageTransition>
//       <div className="pt-28 pb-16">
//         <section className="container-luxury">
//           <AnimatedSection>
//             <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">Contact</h1>
//             <p className="text-muted-foreground text-sm tracking-wider mb-16">We'd love to hear from you</p>
//           </AnimatedSection>

//           <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">
//             {/* Form */}
//             <AnimatedSection>
//               <form onSubmit={handleSubmit} className="space-y-6">
//                 <div>
//                   <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Name</label>
//                   <input
//                     type="text"
//                     value={form.name}
//                     onChange={(e) => setForm({ ...form, name: e.target.value })}
//                     className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors"
//                   />
//                   {errors.name && <p className="text-destructive text-xs mt-1">{errors.name}</p>}
//                 </div>
//                 <div>
//                   <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Email</label>
//                   <input
//                     type="email"
//                     value={form.email}
//                     onChange={(e) => setForm({ ...form, email: e.target.value })}
//                     className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors"
//                   />
//                   {errors.email && <p className="text-destructive text-xs mt-1">{errors.email}</p>}
//                 </div>
//                 <div>
//                   <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">Message</label>
//                   <textarea
//                     value={form.message}
//                     onChange={(e) => setForm({ ...form, message: e.target.value })}
//                     rows={5}
//                     className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors resize-none"
//                   />
//                   {errors.message && <p className="text-destructive text-xs mt-1">{errors.message}</p>}
//                 </div>
//                 <button
//                   type="submit"
//                   disabled={sending}
//                   className="flex items-center gap-2 px-8 py-3 bg-primary text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300 disabled:opacity-50"
//                 >
//                   <Send size={16} />
//                   {sending ? "Sending..." : "Send Message"}
//                 </button>
//               </form>
//             </AnimatedSection>

//             {/* Info */}
//             <AnimatedSection delay={0.2}>
//               <div className="space-y-10">
//                 <div>
//                   <h3 className="font-display text-xl tracking-wider mb-3">Visit Us</h3>
//                   <div className="flex items-start gap-3 text-muted-foreground">
//                     <MapPin size={18} className="mt-0.5 shrink-0" />
//                     <p className="text-sm leading-relaxed">
//                       42 Via della Moda<br />
//                       Milan, Italy 20121
//                     </p>
//                   </div>
//                 </div>
//                 <div>
//                   <h3 className="font-display text-xl tracking-wider mb-3">Email</h3>
//                   <div className="flex items-center gap-3 text-muted-foreground">
//                     <Mail size={18} />
//                     <a href="mailto:hello@aeterna.com" className="text-sm hover:text-accent transition-colors">
//                       hello@aeterna.com
//                     </a>
//                   </div>
//                 </div>
//                 <div>
//                   <h3 className="font-display text-xl tracking-wider mb-3">Follow</h3>
//                   <div className="flex gap-4">
//                     <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-accent transition-colors">
//                       <Instagram size={20} />
//                     </a>
//                     <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-accent transition-colors">
//                       <Twitter size={20} />
//                     </a>
//                   </div>
//                 </div>

//                 {/* Map embed */}
//                 <div className="mt-8">
//                   <iframe
//                     src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2798.1!2d9.19!3d45.47!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDXCsDI4JzEyLjAiTiA5wrAxMScyNC4wIkU!5e0!3m2!1sen!2sit!4v1"
//                     width="100%"
//                     height="250"
//                     style={{ border: 0 }}
//                     allowFullScreen
//                     loading="lazy"
//                     referrerPolicy="no-referrer-when-downgrade"
//                     title="Store location"
//                     className="grayscale opacity-80"
//                   />
//                 </div>
//               </div>
//             </AnimatedSection>
//           </div>
//         </section>
//       </div>
//     </PageTransition>
//   );
// };

// export default ContactPage;
import { useState } from "react";
import { z } from "zod";
import { toast } from "sonner";
import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import { Instagram, Twitter, MapPin, Mail, Send } from "lucide-react";

const contactSchema = z.object({
  name: z.string().trim().min(1, "Name is required").max(100),
  email: z.string().trim().email("Invalid email address").max(255),
  message: z.string().trim().min(1, "Message is required").max(2000),
});

const ContactPage = () => {
  const [form, setForm] = useState({ name: "", email: "", message: "" });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [sending, setSending] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const result = contactSchema.safeParse(form);
    if (!result.success) {
      const fieldErrors: Record<string, string> = {};
      result.error.errors.forEach((err) => {
        if (err.path[0]) fieldErrors[err.path[0] as string] = err.message;
      });
      setErrors(fieldErrors);
      return;
    }
    setErrors({});
    setSending(true);

    try {
      const res = await fetch("http://localhost:3001/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(result.data),
      });

      const data = await res.json();

      if (res.ok && data.success) {
        setForm({ name: "", email: "", message: "" });
        toast.success("Message sent successfully. We'll be in touch.");
      } else {
        toast.error(data.errors?.[0] || "Something went wrong.");
      }
    } catch (err) {
      toast.error("Could not connect to server. Please try again.");
    } finally {
      setSending(false);
    }
  };

  return (
    <PageTransition>
      <div className="pt-28 pb-16">
        <section className="container-luxury">
          <AnimatedSection>
            <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-4">
              Contact
            </h1>
            <p className="text-muted-foreground text-sm tracking-wider mb-16">
              We'd love to hear from you
            </p>
          </AnimatedSection>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">
            {/* Form */}
            <AnimatedSection>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                  <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">
                    Name
                  </label>
                  <input
                    type="text"
                    value={form.name}
                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                    className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors"
                  />
                  {errors.name && (
                    <p className="text-destructive text-xs mt-1">
                      {errors.name}
                    </p>
                  )}
                </div>
                <div>
                  <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">
                    Email
                  </label>
                  <input
                    type="email"
                    value={form.email}
                    onChange={(e) =>
                      setForm({ ...form, email: e.target.value })
                    }
                    className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors"
                  />
                  {errors.email && (
                    <p className="text-destructive text-xs mt-1">
                      {errors.email}
                    </p>
                  )}
                </div>
                <div>
                  <label className="text-xs tracking-[0.2em] uppercase text-muted-foreground block mb-2">
                    Message
                  </label>
                  <textarea
                    value={form.message}
                    onChange={(e) =>
                      setForm({ ...form, message: e.target.value })
                    }
                    rows={5}
                    className="w-full bg-transparent border-b border-border py-3 text-foreground focus:border-accent outline-none transition-colors resize-none"
                  />
                  {errors.message && (
                    <p className="text-destructive text-xs mt-1">
                      {errors.message}
                    </p>
                  )}
                </div>
                <button
                  type="submit"
                  disabled={sending}
                  className="flex items-center gap-2 px-8 py-3 bg-primary text-primary-foreground text-sm tracking-[0.15em] uppercase hover:bg-accent hover:text-accent-foreground transition-all duration-300 disabled:opacity-50"
                >
                  <Send size={16} />
                  {sending ? "Sending..." : "Send Message"}
                </button>
              </form>
            </AnimatedSection>

            {/* Info */}
            <AnimatedSection delay={0.2}>
              <div className="space-y-10">
                <div>
                  <h3 className="font-display text-xl tracking-wider mb-3">
                    Visit Us
                  </h3>
                  <div className="flex items-start gap-3 text-muted-foreground">
                    <MapPin size={18} className="mt-0.5 shrink-0" />
                    <p className="text-sm leading-relaxed">
                      42 Via della Moda
                      <br />
                      Milan, Italy 20121
                    </p>
                  </div>
                </div>
                <div>
                  <h3 className="font-display text-xl tracking-wider mb-3">
                    Email
                  </h3>
                  <div className="flex items-center gap-3 text-muted-foreground">
                    <Mail size={18} />
                    <a
                      href="mailto:hello@aeterna.com"
                      className="text-sm hover:text-accent transition-colors"
                    >
                      hello@aeterna.com
                    </a>
                  </div>
                </div>
                <div>
                  <h3 className="font-display text-xl tracking-wider mb-3">
                    Follow
                  </h3>
                  <div className="flex gap-4">
                    <a
                      href="https://instagram.com"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-muted-foreground hover:text-accent transition-colors"
                    >
                      <Instagram size={20} />
                    </a>
                    <a
                      href="https://twitter.com"
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-muted-foreground hover:text-accent transition-colors"
                    >
                      <Twitter size={20} />
                    </a>
                  </div>
                </div>

                {/* Map embed */}
                <div className="mt-8">
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.6!2d79.8211!3d6.9271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae253d10f7a7003%3A0x320b2e4d32d3838d!2sColombo%2C%20Sri%20Lanka!5e0!3m2!1sen!2slk!4v1
"
                    width="100%"
                    height="250"
                    style={{ border: 0 }}
                    allowFullScreen
                    loading="lazy"
                    referrerPolicy="no-referrer-when-downgrade"
                    title="Store location"
                    className="grayscale opacity-80"
                  />
                </div>
              </div>
            </AnimatedSection>
          </div>
        </section>
      </div>
    </PageTransition>
  );
};

export default ContactPage;
