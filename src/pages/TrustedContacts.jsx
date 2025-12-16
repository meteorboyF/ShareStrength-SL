import React, { useState } from 'react';
import { Link } from 'react-router-dom';

const INITIAL_CONTACTS = [
  { id: 1, name: "Martha Johnson", relation: "Daughter", phone: "+1 (555) 012-3456", is_primary: true },
  { id: 2, name: "Dr. Stevens", relation: "Family Doctor", phone: "+1 (555) 098-7654", is_primary: false }
];

const TrustedContacts = () => {
  const [contacts, setContacts] = useState(INITIAL_CONTACTS);
  const [showForm, setShowForm] = useState(false);
  const [newContact, setNewContact] = useState({ name: '', relation: '', phone: '' });

  const handleAddContact = (e) => {
    e.preventDefault();
    const contact = {
      id: Date.now(),
      ...newContact,
      is_primary: contacts.length === 0 // First contact is primary by default
    };
    setContacts([...contacts, contact]);
    setNewContact({ name: '', relation: '', phone: '' });
    setShowForm(false);
  };

  const handleDelete = (id) => {
    if (window.confirm("Are you sure you want to remove this contact?")) {
      setContacts(contacts.filter(c => c.id !== id));
    }
  };

  const setPrimary = (id) => {
    setContacts(contacts.map(c => ({
      ...c,
      is_primary: c.id === id
    })));
  };

  return (
    <div className="min-h-screen bg-neutral-light font-sans p-4 sm:p-8">
      <div className="max-w-4xl mx-auto">
        
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
            <div>
                <Link to="/dashboard" className="text-sm font-bold text-neutral-500 hover:text-primary mb-2 inline-block">
                    &larr; Back to Dashboard
                </Link>
                <h1 className="text-3xl font-extrabold text-neutral-darkest">Trusted Contacts</h1>
                <p className="text-neutral-medium">Manage people who can be contacted in an emergency.</p>
            </div>
            <button 
                onClick={() => setShowForm(!showForm)}
                className="bg-primary text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-primary-dark transition"
            >
                {showForm ? 'Cancel' : '+ Add Contact'}
            </button>
        </div>

        {/* Add Contact Form */}
        {showForm && (
            <div className="bg-white p-6 rounded-xl shadow-lg border border-primary/20 mb-8 animate-fade-in-up">
                <h3 className="font-bold text-lg mb-4">New Contact Details</h3>
                <form onSubmit={handleAddContact} className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input 
                        type="text" placeholder="Full Name" required 
                        className="p-3 border border-neutral-200 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        value={newContact.name} onChange={e => setNewContact({...newContact, name: e.target.value})}
                    />
                    <input 
                        type="text" placeholder="Relationship (e.g. Son, Doctor)" required 
                        className="p-3 border border-neutral-200 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        value={newContact.relation} onChange={e => setNewContact({...newContact, relation: e.target.value})}
                    />
                    <input 
                        type="tel" placeholder="Phone Number" required 
                        className="p-3 border border-neutral-200 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        value={newContact.phone} onChange={e => setNewContact({...newContact, phone: e.target.value})}
                    />
                    <button type="submit" className="md:col-span-3 bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
                        Save Contact
                    </button>
                </form>
            </div>
        )}

        {/* Contacts List */}
        <div className="grid gap-4">
            {contacts.length === 0 ? (
                <div className="text-center py-12 bg-white rounded-xl border border-dashed border-neutral-300">
                    <p className="text-neutral-400">No trusted contacts added yet.</p>
                </div>
            ) : (
                contacts.map(contact => (
                    <div key={contact.id} className={`bg-white p-6 rounded-xl shadow-sm border flex flex-col sm:flex-row justify-between items-center gap-4 transition ${contact.is_primary ? 'border-primary ring-1 ring-primary/20' : 'border-neutral-200'}`}>
                        
                        <div className="flex items-center gap-4 w-full sm:w-auto">
                            <div className={`w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl ${contact.is_primary ? 'bg-primary text-white' : 'bg-neutral-100 text-neutral-500'}`}>
                                {contact.name.charAt(0)}
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-neutral-darkest flex items-center gap-2">
                                    {contact.name}
                                    {contact.is_primary && <span className="bg-primary/10 text-primary text-[10px] uppercase px-2 py-0.5 rounded-full">Primary</span>}
                                </h3>
                                <p className="text-sm text-neutral-500">{contact.relation} • {contact.phone}</p>
                            </div>
                        </div>

                        <div className="flex gap-3 w-full sm:w-auto justify-end">
                            {!contact.is_primary && (
                                <button onClick={() => setPrimary(contact.id)} className="text-xs font-bold text-primary hover:underline">
                                    Set as Primary
                                </button>
                            )}
                            <button onClick={() => handleDelete(contact.id)} className="text-xs font-bold text-red-500 hover:text-red-700 hover:underline">
                                Remove
                            </button>
                        </div>
                    </div>
                ))
            )}
        </div>

        {/* Info Box */}
        <div className="mt-8 bg-blue-50 border border-blue-200 p-4 rounded-lg flex gap-3 items-start">
            <svg className="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p className="text-sm text-blue-800">
                <strong>Privacy Note:</strong> Your trusted contacts are only visible to HelpMates <u>after</u> you have officially hired them for a task. They cannot be seen by the public.
            </p>
        </div>

      </div>
    </div>
  );
};

export default TrustedContacts;