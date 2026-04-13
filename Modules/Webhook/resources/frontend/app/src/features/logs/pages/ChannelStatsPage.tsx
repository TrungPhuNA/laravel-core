import React from "react";
import { useParams, Link } from "react-router-dom";
import { 
    LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer,
    AreaChart, Area, PieChart, Pie, Cell, BarChart, Bar
} from "recharts";
import Card from "@shared/ui/Card";
import Button from "@shared/ui/Button";
import Alert from "@shared/ui/Alert";
import { getStats, WebhookStats } from "../services/logsApi";

export default function ChannelStatsPage() {
    const params = useParams();
    const webhookId = Number(params.id ?? 0);
    
    const [loading, setLoading] = React.useState(true);
    const [data, setData] = React.useState<WebhookStats[]>([]);
    const [days, setDays] = React.useState(30);
    const [error, setError] = React.useState<any>(null);

    React.useEffect(() => {
        if (webhookId) {
            loadStats();
        }
    }, [webhookId, days]);

    const loadStats = async () => {
        setLoading(true);
        try {
            const res = await getStats(webhookId, days);
            setData(res);
            setError(null);
        } catch (e) {
            setError(e);
        } finally {
            setLoading(false);
        }
    };

    // Tinh toan tong so lieu
    const totals = data.reduce((acc, curr) => ({
        success: acc.success + Number(curr.success_count),
        failed: acc.failed + Number(curr.failed_count),
        total: acc.total + Number(curr.success_count) + Number(curr.failed_count)
    }), { success: 0, failed: 0, total: 0 });

    const successRate = totals.total > 0 ? ((totals.success / totals.total) * 100).toFixed(1) : "0";

    const COLORS = ['#10b981', '#f43f5e']; // emerald-500, rose-500
    const pieData = [
        { name: 'Thành công', value: totals.success },
        { name: 'Thất bại', value: totals.failed },
    ];

    return (
        <div className="space-y-6 pb-10">
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div className="flex-1">
                    <div className="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                        <Link to="/channels" className="hover:text-sky-600 transition-colors">Danh sách kênh</Link>
                        <span>/</span>
                        <span>Kênh #{webhookId}</span>
                    </div>
                    <h1 className="text-2xl font-black text-slate-900 tracking-tight">Thống kê hoạt động</h1>
                    <p className="text-sm text-slate-500 mt-1">Phân tích tần suất và tỉ lệ thành công của các request nhận về.</p>
                </div>
                
                <div className="flex items-center gap-2 bg-slate-50 p-1 rounded-2xl border border-slate-100">
                    {[7, 30, 90].map(d => (
                        <button
                            key={d}
                            onClick={() => setDays(d)}
                            className={`px-4 py-2 text-xs font-bold rounded-xl transition-all ${
                                days === d 
                                ? 'bg-white text-sky-600 shadow-sm border border-slate-100' 
                                : 'text-slate-500 hover:text-slate-800'
                            }`}
                        >
                            {d} ngày
                        </button>
                    ))}
                </div>
            </div>

            {error && <Alert tone="danger" title="Lỗi tải dữ liệu" details={String(error.message || error)} />}

            {/* Overview Stats */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <StatCard 
                    label="Tổng Requests" 
                    value={totals.total.toLocaleString()} 
                    icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>}
                    color="sky"
                />
                <StatCard 
                    label="Thành công" 
                    value={totals.success.toLocaleString()} 
                    subValue={`${successRate}% thành công`}
                    icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>}
                    color="emerald"
                />
                <StatCard 
                    label="Thất bại" 
                    value={totals.failed.toLocaleString()} 
                    subValue="Cần kiểm tra lại log lỗi"
                    icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>}
                    color="rose"
                />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Main chart */}
                <Card 
                    title="Biến động theo thời gian" 
                    className="lg:col-span-2 rounded-3xl overflow-hidden border-slate-100 shadow-sm"
                    bodyClassName="h-[400px] p-6"
                >
                    {loading ? (
                        <div className="h-full w-full bg-slate-50 animate-pulse rounded-2xl"></div>
                    ) : (
                        <ResponsiveContainer width="100%" height="100%">
                            <AreaChart data={data} margin={{ top: 10, right: 10, left: 0, bottom: 0 }}>
                                <defs>
                                    <linearGradient id="colorSuccess" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#10b981" stopOpacity={0.1}/>
                                        <stop offset="95%" stopColor="#10b981" stopOpacity={0}/>
                                    </linearGradient>
                                    <linearGradient id="colorFailed" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="5%" stopColor="#f43f5e" stopOpacity={0.1}/>
                                        <stop offset="95%" stopColor="#f43f5e" stopOpacity={0}/>
                                    </linearGradient>
                                </defs>
                                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                                <XAxis 
                                    dataKey="date" 
                                    axisLine={false} 
                                    tickLine={false} 
                                    tick={{fontSize: 10, fill: '#94a3b8'}}
                                    tickFormatter={(val) => {
                                        const parts = val.split('-');
                                        return `${parts[2]}/${parts[1]}`;
                                    }}
                                />
                                <YAxis axisLine={false} tickLine={false} tick={{fontSize: 10, fill: '#94a3b8'}} />
                                <Tooltip 
                                    contentStyle={{ borderRadius: '16px', border: 'none', boxShadow: '0 10px 15px -3px rgba(0,0,0,0.1)' }}
                                    itemStyle={{ fontSize: '12px', fontWeight: 'bold' }}
                                />
                                <Legend verticalAlign="top" height={36} iconType="circle" />
                                <Area 
                                    type="monotone" 
                                    dataKey="success_count" 
                                    name="Thành công" 
                                    stroke="#10b981" 
                                    strokeWidth={3}
                                    fillOpacity={1} 
                                    fill="url(#colorSuccess)" 
                                />
                                <Area 
                                    type="monotone" 
                                    dataKey="failed_count" 
                                    name="Thất bại" 
                                    stroke="#f43f5e" 
                                    strokeWidth={3}
                                    fillOpacity={1} 
                                    fill="url(#colorFailed)" 
                                />
                            </AreaChart>
                        </ResponsiveContainer>
                    )}
                </Card>

                {/* Pie Chart */}
                <Card 
                    title="Tỉ trọng trạng thái" 
                    className="rounded-3xl border-slate-100 shadow-sm"
                    bodyClassName="h-[400px] p-6 flex flex-col items-center justify-center"
                >
                    {loading ? (
                        <div className="h-48 w-48 rounded-full bg-slate-50 animate-pulse"></div>
                    ) : totals.total === 0 ? (
                        <div className="text-slate-400 text-sm italic">Không có dữ liệu</div>
                    ) : (
                        <>
                            <ResponsiveContainer width="100%" height="80%">
                                <PieChart>
                                    <Pie
                                        data={pieData}
                                        cx="50%"
                                        cy="50%"
                                        innerRadius={60}
                                        outerRadius={80}
                                        paddingAngle={8}
                                        dataKey="value"
                                    >
                                        {pieData.map((entry, index) => (
                                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} cornerRadius={8} />
                                        ))}
                                    </Pie>
                                    <Tooltip />
                                </PieChart>
                            </ResponsiveContainer>
                            <div className="w-full space-y-2 mt-4">
                                <PieStatItem label="Thành công" value={totals.success} color="bg-emerald-500" percent={successRate} />
                                <PieStatItem label="Thất bại" value={totals.failed} color="bg-rose-500" percent={(100 - Number(successRate)).toFixed(1)} />
                            </div>
                        </>
                    )}
                </Card>
            </div>
            
            <div className="flex justify-center">
                <Link to={`/webhook/channels/${webhookId}/logs`}>
                    <Button variant="ghost" className="text-sky-600 font-bold text-sm">
                        Xem chi tiết logs &rarr;
                    </Button>
                </Link>
            </div>
        </div>
    );
}

function StatCard({ label, value, subValue, icon, color }: any) {
    const colors: any = {
        sky: 'bg-sky-50 text-sky-600 border-sky-100/50',
        emerald: 'bg-emerald-50 text-emerald-600 border-emerald-100/50',
        rose: 'bg-rose-50 text-rose-600 border-rose-100/50',
    };
    
    return (
        <div className={`p-6 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5`}>
            <div className={`h-14 w-14 rounded-2xl flex items-center justify-center border ${colors[color]}`}>
                {icon}
            </div>
            <div>
                <div className="text-xs font-bold text-slate-400 uppercase tracking-widest">{label}</div>
                <div className="text-2xl font-black text-slate-900 mt-0.5">{value}</div>
                {subValue && <div className="text-[10px] font-bold text-slate-500 mt-1">{subValue}</div>}
            </div>
        </div>
    );
}

function PieStatItem({ label, value, color, percent }: any) {
    return (
        <div className="flex items-center justify-between text-xs p-2 rounded-xl hover:bg-slate-50 transition-colors">
            <div className="flex items-center gap-2">
                <div className={`w-2 h-2 rounded-full ${color}`}></div>
                <span className="font-bold text-slate-600">{label}</span>
            </div>
            <div className="flex items-center gap-3">
                <span className="text-slate-400 font-mono">{value}</span>
                <span className="font-black text-slate-900 w-12 text-right">{percent}%</span>
            </div>
        </div>
    );
}
