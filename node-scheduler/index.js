const cron = require('node-cron');
const axios = require('axios');
const https = require('https');
const fs = require('fs');
const config = require('./config');

//const { baseUrl, baseESUrl } = require('../config');

// Konfigurasi SSL
const agent = new https.Agent({
    rejectUnauthorized: false, // Setel ke true jika sertifikat SSL diharuskan
    // Path ke sertifikat SSL
    // cert: fs.readFileSync('/path/to/client.crt'),
    // key: fs.readFileSync('/path/to/client.key'),
    ca: fs.readFileSync('../backend/ca.crt')
});

const username = config.es_username;
const password = config.es_password;
const baseUrl = config.base_url;
const baseESUrl = config.base_es_url;

async function searchElasticsearch() {
    try {
        let response = await axios.post(baseESUrl + '/edocs_*/_search', {
            // size: 3, // Jumlah data yang ingin diambil dalam setiap permintaan
            query: {
                range: {
                    "valid_until": {
                        "lte": "now+90d",
                        "gt": "now/d"
                    }
                }
            },
            sort: [
                { "valid_until": 'asc' },
                { "created_at": 'asc' }
            ], // Sortir berdasarkan _doc
        }, {
            auth: {
                username: username,
                password: password
            },
            httpsAgent: agent
        });

        const totalHits = response.data.hits.total.value;
        let searchData = response.data.hits.hits;

        while (searchData.length < totalHits) {
            const lastHit = searchData[searchData.length - 1].sort;
            response = await axios.post(baseESUrl + '/edocs_*/_search', {
                // size: 3, // Jumlah data yang ingin diambil dalam setiap permintaan
                query: {
                    range: {
                        "valid_until": {
                            "lte": "now+90d",
                            "gt": "now/d"
                        }
                    }
                },
                sort: [
                    { "valid_until": 'asc' },
                    { "created_at": 'asc' }
                ], // Sortir berdasarkan _doc
                search_after: lastHit
            }, {
                auth: {
                    username: username,
                    password: password
                },
                httpsAgent: agent
            });

            searchData = [...searchData, ...response.data.hits.hits];
        }
        // const docNames = searchData.map(item => item._source.doc_name);

        // console.log('Data Pencarian:', docNames);
        return searchData;
    } catch (error) {
        console.error('Kesalahan dalam permintaan pencarian:', error);
    }
}

async function login() {
    try {
        let response = await axios.post(baseUrl + '/backend/users/login', {
            username: config.user_login,
            password: config.password_login
        }, {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            httpsAgent: agent
        });

        const cookieString = response.headers['set-cookie']
        const token = cookieString[0].match(/edocs_auth=([^;]+)/)[1];
        return token
    } catch (error) {
        console.error('Login failed:', error);
    }
}

function sendEmail(element, token) {
    try {
        const departmentIds = element._source.departments.map(department => department.id);
        axios.post(baseUrl + '/backend/email/send-notification-email', {
                recipient_departments: departmentIds,
                message: 'Dokumen dengan judul <strong>' + element._source.doc_name + '</strong> akan segera kadaluarsa dalam <strong>' + getDayDifference(element._source.valid_until) + ' hari</strong>. Pastikan untuk mengambil tindakan yang diperlukan sebelum tanggal kadaluwarsa.',
                email_to: element._source.email_to,
                email_cc: element._source.email_cc,
            }, {
                headers: {
                    edocs_auth: token,
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                httpsAgent: agent
            })
            .then(function(response) {
                console.log(response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
    } catch (error) {
        console.error('Send email failed:', error);
    }
}


cron.schedule('0 6 * * *', async() => {
    // cron.schedule('*/1 * * * *', async() => {
    const response = await searchElasticsearch()

    const data = response.filter(element => {
        var selisih = getDayDifference(element._source.valid_until)
        console.log(element._source.doc_name + ';selisih:' + selisih)
            // var selisih = getMinuteDifference('2023-07-11T14:21:00')
        return ([90, 60, 30, 21, 14, 7]).includes(selisih)
            // return ([9, 6, 3, 2, 1]).includes(selisih)
    })

    if (data.length > 0) {
        // console.log(data)
        let token = await login();

        console.log(token)

        data.forEach(element => {
            // const recipients = await getRecepientsByDepts(element._source.departments);
            sendEmail(element, token)
        });
    }
});

function getDayDifference(givenDate) {
    var currentDate = new Date(); // Tanggal saat ini
    var convertedDate = new Date(givenDate); // Tanggal yang diberikan

    // Menghitung selisih dalam milidetik, lalu mengkonversi ke hari
    var differenceInTime = convertedDate.getTime() - currentDate.getTime();
    var differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));

    return differenceInDays;
}

function getMinuteDifference(endDate) {
    const start = new Date();
    const end = new Date(endDate);

    // console.log('current ' + start)
    // console.log('given ' + end)
    const diffInMilliseconds = end - start;
    const minutes = Math.floor(diffInMilliseconds / (1000 * 60));

    // console.log('return ' + minutes)
    return minutes;
}