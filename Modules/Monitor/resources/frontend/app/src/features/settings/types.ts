export type MonitorSettings = {
    check: {
        rdap: { enabled: boolean };
        whois: { enabled: boolean };
        third_party: { enabled: boolean; api_key: string };
    };
    warning: {
        normal_days: number;
        soon_days: number;
        critical_days: number;
    };
};